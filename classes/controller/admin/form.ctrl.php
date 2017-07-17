<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\Form;

use Nos\User\Permission;

class Controller_Admin_Form extends \Nos\Controller_Admin_Crud
{
    protected $to_delete = array();
    protected $fields_fieldset = array();
    protected $fields_data = array();

    public function prepare_i18n()
    {
        parent::prepare_i18n();
        \Nos\I18n::current_dictionary('noviusos_form::common');
    }

    protected function init_item()
    {
        $this->item->form_captcha = 1;
        parent::init_item();
    }

    /**
     * Before saving the form
     *
     * @param $item
     * @param $data
     * @throws \Exception
     * @throws \FuelException
     */
    public function before_save($item, $data)
    {
        $emails = explode("\n", $item->form_submit_email);
        $item->form_submit_email = '';
        foreach ($emails as $email) {
            $email = trim($email);
            if (empty($email)) {
                continue;
            }
            if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                $item->form_submit_email .= $email."\n";
            } else {
                throw new \Exception('An email which receive answers is not valid.');
            }
        }

        // Gets the fields data (the field data is json encoded, see Pull Request #15)
        $fields_post = \Input::post('fields', null);
        if (empty($fields_post)) {
            throw new \Exception(__('Error: Your form seems to have no field.'));
        }
        $fields_data = json_decode($fields_post, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            logger(\Fuel::L_ERROR, 'Error: invalid fields format. JSON Error: '.json_last_error_msg());
        }
        if (empty($fields_data)) {
            throw new \Exception(__('Your form must have at least one field.'));
        }

        // Formats fields data
        foreach ($fields_data as $field_id => $field_data) {
            // The default_value from POST is a comma-separated string of the indexes
            // We want to store textual values (separated by \n for the multiple values of checkboxes)
            if (!empty($field_data['field_choices'])) {
                $choices = preg_split('`\r\n|\r|\n`', $field_data['field_choices']);
                // Check possible values in choices
                $choiceList = array();
                foreach ($choices as $index => $choice) {
                    $choiceInfos = preg_split('~(?<!\\\)=~', $choice);
                    foreach ($choiceInfos as $key => $choiceValue) {
                        $choiceInfos[$key] = str_replace('\=', '=', $choiceValue);
                    }
                    $choiceList[] = \Arr::get($choiceInfos, 1, $index);
                }
                $choices = array_combine($choiceList, $choiceList);

                $default_value = explode(',', $field_data['field_default_value']);
                $default_value = array_combine($default_value, $default_value);
                $fields_data[$field_id]['field_default_value'] = implode("\n", array_intersect_key($choices, $default_value));
            }
        }

        // Deletes missing fields
        $this->to_delete = array_diff(
            array_keys($item->fields),
            \Arr::pluck($fields_data, 'field_id')
        );

        // Registers the fields
        $fieldsConfig = \Arr::get($this->config, 'fields_config.fields');
        foreach ($fields_data as $field_id => $field_data) {
            $field = Model_Field::find($field_id);

            // Builds the fields config with the driver's config
            $fieldFieldsConfig = \Arr::merge($fieldsConfig, \Arr::get($field->getDriver()->getConfig(), 'admin.fields', array()));

            $this->fields_fieldset[$field_id] = \Fieldset::build_from_config($fieldFieldsConfig, array(
                'save' => false,
            ));
            $this->fields_data[$field_id] = $field_data;
            $item->fields[$field_id] = $field;
        }
    }

    /**
     * Saves the form
     *
     * @param $item
     * @param $data
     * @return \Nos\Array
     */
    public function save($item, $data)
    {
        $return = parent::save($item, $data);

        // Save the form registered fields
        foreach ($this->fields_fieldset as $field_id => $fieldset) {
            if (in_array($field_id, $this->to_delete)) {
                continue;
            }
            $field = $item->fields[$field_id];
            $field->field_form_id = $item->form_id;
            $fieldset->validation()->run($this->fields_data[$field_id]);
            $fieldset->triggerComplete($field, $fieldset->validated());
        }

        // Delete fields
        foreach ($this->to_delete as $field_id) {
            $item->fields[$field_id]->delete();
            unset($item->fields[$field_id]);
        }
        $this->to_delete = array();

        return $return;
    }

    public function action_render_fields_blank_slate()
    {
        $availableFields = Helper_Admin_Form::getAvailableFields();
        $availableTemplates = Helper_Admin_Form::getAvailableTemplates();

        return \View::forge('noviusos_form::admin/form/fields_blank_slate', array(
            'layouts' => array(
                array(
                    'title' => __('Standard fields'),
                    'layout' => array_filter($availableFields, function ($field) {
                        return empty($field['special']);
                    }),
                ),
                array(
                    'title' => __('Special fields'),
                    'layout' => array_filter($availableFields, function ($field) {
                        return !empty($field['special']);
                    }),
                ),
                array(
                    'title' => __('Fields layout'),
                    'layout' => $availableTemplates,
                ),
            ),
        ), false);
    }

    /**
     * Renders the specified layout
     *
     * @param $layoutName
     * @throws \Exception
     */
    public function action_render_layout($layoutName)
    {
        $layoutName = base64_decode($layoutName);

        // Gets the app config
        $appConfig = \Config::load('noviusos_form::config', true);

        // Gets the field definition
        if ($layoutName == 'default') {
            $definition = \Arr::get($appConfig, 'default_fields_layout.definition', array());
        } else {
            $definition = \Arr::get(Helper_Admin_Form::getAvailableFields(), $layoutName.'.definition',
                \Arr::get(Helper_Admin_Form::getAvailableTemplates(), $layoutName.'.definition', array())
            );
        }
        if (empty($definition)) {
            throw new \Exception('Field definition not found.');
        }

        // Creates the fields meta, preview and layout
        $fields = array();
        $previews = array();
        $layout = \Arr::get($definition, 'layout');
        foreach (\Arr::get($definition, 'fields', array()) as $field_identifier => $field_properties) {

            // Gets the driver
            $driverClass = \Arr::get($field_properties, 'driver');
            if (empty($driverClass)) {
                throw new \Exception('The `driver` key is missing in the driver configuration.');
            }

            // Builds the field data
            $field_data = \Arr::get($field_properties, 'default_values');
            $field_data['field_driver'] = $driverClass;

            // Creates the field in database
            $field = $this->create_field_db($field_data);

            // Renders the field
            $fields[] = (string) $this->render_field_meta($field);
            $previews[] = $this->render_field_preview($field);

            // Replaces the field identifier by the primary key in the layout
            $layout = str_replace($field_identifier, $field->field_id, $layout);
        }

        \Response::json(array(
            'fields' => $fields,
            'previews' => $previews,
            'layout' => $layout,
        ));
    }

    /**
     * @deprecated Please use action_render_layout() instead
     *
     * @param $meta
     * @throws \Exception
     */
    public function action_form_field_meta($meta)
    {
        $this->action_render_layout($meta);
    }

    /**
     * Renders the field
     *
     * @param $field_id
     * @throws \Exception
     * @throws \FuelException
     */
    public function action_render_field($field_id)
    {
        // Gets the field
        $field = Model_Field::find($field_id);
        if (empty($field)) {
            throw new \Exception('Field not found');
        }

        // Injects the specified field data
        $fieldData = $this->getInputFieldData();
        \Arr::delete($fieldData, 'field_id');
        if (!empty($fieldData)) {
            $field->set($fieldData);
        }

        // Renders the field
        $html = (string) $this->render_field_meta($field);

        \Response::json(array(
            'meta' => $html,
            'preview' => $this->render_field_preview($field),
        ));
    }

    /**
     * Handles the request to displays a new field in a JSON response
     *
     * @param Model_Field $field
     * @throws \Exception
     */
    public function action_render_field_preview($field)
    {
        if (!is_a($field, Model_Field::class)) {
            $field_id = intval($field);

            // Gets the field or forge a new one
            if (!empty($field_id)) {
                $field = Model_Field::find($field_id);
                if (empty($field)) {
                    throw new \Exception(__('Field not found.'));
                }
            } else {
                $field = Model_Field::forge();
            }
        }

        // Gets the field data
        $fieldData = $this->getInputFieldData();
        if (!empty($fieldData)) {
            \Arr::delete($fieldData, 'field_id');
            // Sets the field data
            $field->set($fieldData);
        }

        $preview = $this->render_field_preview($field);

        \Response::json(array(
            'preview' => $preview,
        ));
    }

    /**
     * Handles the request to render the specified field
     *
     * @param $field
     * @return string
     */
    public function action_render_field_meta($field)
    {
        return $this->render_field_meta($field);
    }

    /**
     * Exports the form answers
     *
     * @param $id
     * @throws
     */
    public function action_export($id)
    {
        set_time_limit(5 * 60);

        try {
            $helper = new Helper_Export();
            $helper->parseForm($id);
            $headers = $helper->headers;

            $csv = array(
                'header' => array(),
            );

            foreach ($headers as $header) {
                $csv['header'][] = $header['label'];
                if (!empty($header['choices'])) {
                    if (!isset($csv['choices'])) {
                        $csv['choices'] = array();
                    }
                    $fill = count($csv['header']) - 1;
                    $fill > 0 and $csv['choices'] = array_pad($csv['choices'], $fill, '');
                    foreach ($header['choices'] as $choice) {
                        $csv['choices'][] = $choice;
                    }
                    $csv['header'] = array_pad($csv['header'], count($csv['header']) + count($header['choices']) - 1, '');
                }
            }

            // Disable response buffering
            $level = ob_get_level();
            for ($i = 0; $i < $level; $i++) {
                ob_end_clean();
            }

            // Enable garbage collector
            gc_enable();

            // Send HTTP headers for inform the browser that it will receive a CSV file
            \Response::forge(\Format::forge($csv)->to_csv()."\n", 200, array(
                'Content-Type'              => 'application/csv',
                'Content-Disposition'       => 'attachment; '.
                    'filename='.\Nos\Orm_Behaviour_Virtualname::friendly_slug($this->crud_item($id)->form_name).'.csv;',
                'Content-Transfer-Encoding' => 'binary',
            ))->send(true);

            while (($values = $helper->getValues())) {
                $csv = array();
                foreach ($values as $value) {
                    $csvValue = array();
                    foreach ($value as $key => $valueContent) {
                        if (!is_array($valueContent)) {
                            $csvValue[] = $valueContent;
                        } else {
                            $csvValue += \Arr::merge($csvValue, $valueContent);
                        }
                    }
                    $csv[] = $csvValue;
                }
                \Response::forge(\Format::forge($csv)->to_csv()."\n")->send();
            }
            exit();
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }

    /**
     * Renders the field preview
     *
     * @param Model_Field $field
     * @return string
     */
    protected function render_field_preview(Model_Field $field)
    {
        return Service_Field::forge($field)->getPreviewHtml($this->enhancer_args);
    }

    /**
     * Renders the field meta
     *
     * @param Model_Field $field
     * @return string
     */
    protected function render_field_meta(Model_Field $field)
    {
        static $auto_id_increment = 1;

        // Gets the fields meta config
        $fieldsMetaConfig = $field->getDriver()->getAdminFieldsMetaConfig();

        // If the field driver is not in the driver list, then hides the driver list
        $driverOptions = \Arr::get($fieldsMetaConfig, 'field_driver.form.options', array());
        if (!isset($driverOptions[$field->getDriverClass()])) {
            \Arr::set($fieldsMetaConfig, 'field_driver.form.type', 'hidden');
        }

        // Gets the layout meta config
        $layoutMetaConfig = $field->getDriver()->getAdminLayoutMetaConfig();

        // Builds the fieldset
        $fieldset = \Fieldset::build_from_config($fieldsMetaConfig, $field, array('save' => false, 'auto_id' => false));

        // Override auto_id generation so it don't use the name (because we replace it below)
        $auto_id = uniqid('auto_id_');
        foreach ($fieldset->field() as $fieldsetField) {
            if ($fieldsetField->get_attribute('id') == '') {
                $fieldsetField->set_attribute('id', $auto_id.$auto_id_increment++);
            }
        }

        // Builds the view params
        $fields_view_params = array(
            'fieldset' => $fieldset,
            'layout' => $layoutMetaConfig,
            'js_file' => \Arr::get($field->getDriver()->getConfig(), 'admin.js_file'),
            'field' => $field,
        );

        $fields_view_params['view_params'] = &$fields_view_params;

        // Renders the field content
        $content = (string) \View::forge('noviusos_form::admin/form/field', $fields_view_params, false)->render();
        $content .= $fieldset->build_append();

        // Injects the field ID in the field virtual name (eg. name="field[field_xxx][]" => name="field[field_xxx][12345]")
        $replaces = array();
        foreach ($fieldsMetaConfig as $name => $fieldMetaConfig) {
            $replaces[$name] = "field[{$field->field_id}][$name]";
        }
        $content = strtr($content, $replaces);

        return $content;
    }

    /**
     * Creates a field in database
     *
     * @param array $data
     * @return Model_Field
     * @throws \Exception
     */
    protected function create_field_db($data = array())
    {
        $default_data = array(
            'field_form_id' => '0',
            'field_virtual_name' => uniqid(),
        );

        // Gets the default values
        foreach (\Arr::get($this->config, 'fields_config.fields') as $name => $field) {
            if (!empty($field['dont_save']) || (!empty($field['form']['type']) && $field['form']['type'] == 'submit')) {
                continue;
            }
            $default_data[$name] = \Arr::get($field, 'form.value', '');
        }

        unset($default_data['field_id']);
        $default_data['field_mandatory'] = 0;
        $default_data['field_conditional'] = 0;

        // Creates and saves the field
        $model_field = Model_Field::forge(array_merge($default_data, $data), true);
        $model_field->save();

        return $model_field;
    }

    /**
     * Gets the input field data (JSON or array)
     *
     * @param string $name
     * @return array
     */
    protected function getInputFieldData($name = 'fieldData')
    {
        // Gets the field data
        $fieldData = \Input::post($name);
        if (empty($fieldData)) {
            return array();
        }

        // Json decodes if it's a string
        if (is_string($fieldData)) {
            $fieldData = (array) json_decode($fieldData);
        }

        if (!is_array($fieldData)) {
            return array();
        }

        // Flatten the array
        $fieldData = \Arr::pluck($fieldData, 'value', 'name');

        return $fieldData;
    }

    public function action_duplicate($id = null)
    {
        try {
            /**
             * @var $form Model_Form
             */
            $form = $this->crud_item($id);
            $contexts = Permission::contexts();
            $duplicateContext = (string) \Input::post('duplicate_context');
            // Check context permission with selected context target
            if (!empty($duplicateContext) && !array_key_exists($duplicateContext, $contexts)) {
                throw new \Exception(__('Invalid context selected.'));
            }
            // No asking popup if only 1 context / duplicate if valid target context was chosen
            if (count($contexts) === 1 || !empty($duplicateContext)) {
                $context = !empty($duplicateContext) ? $duplicateContext : $form->form_context;
                $form->duplicate($context);
                // Send response
                \Response::json(array(
                    'dispatchEvent' => array(
                        'name' => 'Nos\Form\Model_Form',
                        'action' => 'insert',
                        'context' => $context,
                    ),
                    'notify' => __('Here you are! The form has just been duplicated.'),
                ));
            } else {
                \Response::json(array(
                    'action' => array(
                        'action' => 'nosDialog',
                        'dialog' => array(
                            'ajax' => true,
                            'contentUrl' => 'admin/noviusos_form/form/popup_duplicate/'.$id,
                            'title' => strtr(__('Duplicating the form "{{title}}"'), array(
                                '{{title}}' => \Str::truncate($form->title_item(), 40),
                            )),
                            'width' => 500,
                            'height' => 200,
                        ),
                    ),
                ));
            }
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }

    /**
     * Return popup content to ask the target context of duplication
     *
     * @param null $id : the ID of Model_Form to duplicate
     * @return \Fuel\Core\View
     */
    public function action_popup_duplicate($id = null)
    {
        /**
         * @var $form Model_Form
         */
        $form = $this->crud_item($id);
        $contexts_list = array_keys(Permission::contexts());

        return \View::forge('noviusos_form::admin/popup_duplicate', array(
            'item' => $form,
            'action' => 'admin/noviusos_form/form/duplicate/'.$id,
            'contexts_list' => $contexts_list,
        ), false);
    }

    /**
     * Display a popup to confirm deletion of form or of form's answers
     * If request is POST, it's a deletion confirmation : we make the deletion
     *
     * @param type $id : the id of form
     * @return type View : the popup
     */
    public function action_delete($id = null)
    {
        if (\Input::method() === 'POST' && (int) \Input::post('delete_answers', 0) === 1) {
            return $this->action_delete_answers($id);
        } else {
            return parent::action_delete($id);
        }
    }

    /**
     * Display a popup to confirm answers' deletion
     *
     * @param type $id : the id of form
     * @return type View : the popup
     */
    public function action_delete_answers($id = null)
    {
        try {
            if (\Input::method() === 'POST') {
                $this->deleteAnswers((int) \Input::post('id', 0));
            } else {
                $this->item = $this->crud_item($id);
                $this->checkPermission('delete_answers');

                if (!$this->item->getAnswersCount()) {
                    throw new \Exception(__('There is no answer yet.'));
                }

                $viewsParams = $this->view_params();
                $formCommonConfig = \Config::load('noviusos_form::common/form', true);
                $i18nOverride = \array_merge($viewsParams['crud']['config']['i18n'], \Arr::get($formCommonConfig, 'i18n_answers_deletion', array()));

                $viewsParams['crud']['config']['views']['delete'] = 'noviusos_form::admin/form/popup_delete_answers';
                $viewsParams['crud']['config']['i18n'] = $i18nOverride;


                return \View::forge('nos::crud/delete_popup_layout', $viewsParams, false);
            }
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }

    /**
     * Delete all answers of a given form
     *
     * @param integer $formID
     */
    protected function deleteAnswers($formID)
    {
        try {
            $this->item = $this->crud_item((int) $formID);
            $this->checkPermission('delete_answers');

            if (empty($this->item->id)) {
                throw new \Exception(__('Unable to find the form.'));
            }

            if (!$this->item->getAnswersCount()) {
                throw new \Exception(__('This form has no answer.'));
            }

            // Remove all answers
            $this->item->answers = array();
            $this->item->save();

            \Response::json(array(
                'dispatchEvent' => array(
                    array(
                        'name' => 'Nos\\Form\\Model_Answer',
                    ),
                    array(
                        'name' => 'Nos\\Form\\Model_Form',
                        'action' => 'delete',
                        'id' => $this->item->id,
                        'context' => $this->item->form_context,
                    ),
                ),
                'notify' => __('The answers have been deleted.'),
            ));
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }
}
