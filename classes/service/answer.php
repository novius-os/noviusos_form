<?php

namespace Nos\Form;

class Service_Answer
{
    /**
     * @var Model_Answer
     */
    protected $answer;

    /**
     * Constructor
     *
     * @param Model_Answer $answer
     */
    public function __construct(Model_Answer $answer)
    {
        $this->answer = $answer;
    }

    /**
     * Forges a new instance
     *
     * @param Model_Answer $answer
     * @return static
     */
    public static function forge(Model_Answer $answer)
    {
        return new static($answer);
    }

    /**
     * Forges a new answer instance
     *
     * @param Model_Form $form
     * @return Model_Answer
     */
    public static function forgeAnswer(Model_Form $form)
    {
        // Forges the answer
        $answer = Model_Answer::forge();
        $answer->ip = \Input::real_ip();
        $answer->form = $form;

        return $answer;
    }

    /**
     * Saves the answer
     *
     * @param Model_Field[] $fields
     * @param array $data
     * @param array $options
     * @return bool
     */
    public function saveAnswer(array $fields, array $data, $options = array())
    {
        // Executes fields pre-processing on answer
        foreach ($fields as $name => $field) {
            // Handles fields pre processing on answer
            $field->getDriver($options)->beforeAnswerSave($this->answer, \Arr::get($data, $name), $data);
        }

        // Saves the answer
        $this->answer->save();

        // Executes fields post-processing on answer
        foreach ($fields as $name => $field) {
            $fieldDriver = $field->getDriver($options);

            // Handles fields post processing on answer
            $fieldDriver->afterAnswerSave($this->answer, \Arr::get($data, $name), $data);

            // Handles fields attachments on answer
            if ($fieldDriver instanceof Interface_Driver_Field_Attachment) {
                $fieldDriver->saveAttachments($this->answer, \Arr::get($data, $name), $data);
            }
        }

        // Creates and saves the answer fields
        $this->answerFields = array();
        foreach ($fields as $name => $field) {
            Model_Answer_Field::forge(array(
                'anfi_answer_id' => $this->answer->id,
                'anfi_field_id' => $field->id,
                'anfi_field_driver' => $field->driver,
                'anfi_value' => \Arr::get($data, $name),
            ), true)->save();
        }

        return true;
    }

    /**
     * Sends a notification by mail to the form recipients
     *
     * @param array $fields
     * @param array $data
     * @param array $options The form options (eg. label position, message...)
     * @return bool
     */
    public function sendNotificationByMail(array $fields, array $data, $options = array())
    {
        // Gets the form
        $form = $this->answer->form;
        if (empty($form)) {
            return false;
        }

        // Gets the recipient list
        $config = \Config::load('noviusos_form::config', true);
        $recipients = array_filter(explode("\n", $form->form_submit_email), function ($var) {
            $var = trim($var);

            return !empty($var) && filter_var($var, FILTER_VALIDATE_EMAIL);
        });

        // Sends an email if there is at least one recipient in list
        if (empty($recipients)) {
            return false;
        }

        // Creates the mail
        $mail = \Email::forge();

        $email_data = array();
        $reply_to_auto = \Arr::get($config, 'add_replyto_to_first_email', true);
        $reply_to = '';
        $attachments = array();
        foreach ($data as $name => $value) {
            $field = \Arr::get($fields, $name);

            $fieldDriver = $field->getDriver($options);

            // Gets the first non empty email as potential "reply_to"
            if ($reply_to_auto && empty($reply_to) && $fieldDriver instanceof Interface_Driver_Field_Email) {
                $reply_to = $fieldDriver->getEmail($value);
            }

            // Gets the field attachments
            if ($fieldDriver instanceof Interface_Driver_Field_Attachment) {
                $attachments = \Arr::merge($attachments, $fieldDriver->getAttachments($this->answer));
            }

            // Builds the field email data
            $email_data[] = $fieldDriver->getEmailData($value, $this->answer);
        }

        // Adds attachments to the mail
        if (!empty($attachments)) {
            // Calculates total size of attachments
            $totalSize = array_sum(array_map(function ($attachment) {
                return filesize($attachment->path());
            }, $attachments));
            // Checks if total size of attachments does not exceed the limit
            if ($totalSize <= \Arr::get($config, 'mail_attachments_max_size', 8388608)) {
                // Adds each attachment to the mail
                foreach ($attachments as $attachment) {
                    $mail->attach($attachment->path());
                }
            }
        }

        // Sets recipient list as BCC
        $mail->bcc($recipients);

        if (!empty($reply_to)) {
            $mail->reply_to($reply_to);
        }

        // Sets subject
        $mail->subject(strtr(__('{{form}}: New answer'), array(
            '&nbsp;' => ' ',
            '{{form}}' => $form->form_name,
        )));

        // Sets body
        $mail->html_body(\View::forge('noviusos_form::front/form/notification/email', array(
            'form' => $form,
            'data' => $email_data,
        ), false));

        // Sends the mail
        try {
            $mail->send();
        } catch (\Exception $e) {
            logger(\Fuel::L_ERROR, 'The Forms application cannot send emails - '.$e->getMessage());

            return false;
        }

        return true;
    }
}
