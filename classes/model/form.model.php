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

class Model_Form extends \Nos\Orm\Model
{
    protected static $_table_name = 'nos_form';
    protected static $_primary_key = array('form_id');
    protected static $_prefix = 'form_';

    protected static $_title_property = 'form_name';
    protected static $_properties = array(
        'form_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'form_context' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'form_name' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'form_virtual_name' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
            'character_maximum_length' => 100,
        ),
        'form_manager_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
        'form_client_email_field_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
        'form_layout' => array(
            'default' => null,
            'data_type' => 'text',
            'null' => false,
        ),
        'form_captcha' => array(
            'default' => null,
            'data_type' => 'tinyint',
            'null' => false,
        ),
        'form_submit_label' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'form_submit_email' => array(
            'default' => null,
            'data_type' => 'text',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
        'form_created_at' => array(
            'default' => null,
            'data_type' => 'datetime',
            'null' => false,
        ),
        'form_updated_at' => array(
            'default' => null,
            'data_type' => 'datetime',
            'null' => false,
        ),
        'form_created_by_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
        'form_updated_by_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
    );

    protected static $_has_one = array();
    protected static $_belongs_to  = array();
    protected static $_many_many = array();
    protected static $_twinnable_has_one = array();
    protected static $_twinnable_has_many = array();
    protected static $_twinnable_belongs_to = array();
    protected static $_twinnable_many_many = array();

    protected static $_observers = array(
        'Orm\\Observer_Self',
        'Orm\\Observer_CreatedAt' => array(
            'mysql_timestamp' => true,
            'property' => 'form_created_at',
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'mysql_timestamp' => true,
            'property' => 'form_updated_at',
        ),
    );

    protected static $_behaviours = array(
        'Nos\Orm_Behaviour_Contextable' => array(
            'context_property'      => 'form_context',
        ),
        'Nos\Orm_Behaviour_Virtualname' => array(
            'virtual_name_property' => 'form_virtual_name',
        ),
        'Nos\Orm_Behaviour_Author' => array(
            'created_by_property' => 'form_created_by_id',
            'updated_by_property' => 'form_updated_by_id',
        ),
    );

    protected static $_has_many = array(
        'fields' => array(
            'key_from'       => 'form_id',
            'model_to'       => 'Nos\Form\\Model_Field',
            'key_to'         => 'field_form_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
        'answers' => array(
            'key_from'       => 'form_id',
            'model_to'       => 'Nos\Form\\Model_Answer',
            'key_to'         => 'answer_form_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
    );

    /**
     * @var null
     */
    protected $_form_id_for_delete = null;

    /**
     * The form service
     *
     * @var Service_Form|null
     */
    protected $service = null;

    /**
     * Gets the form service
     *
     * @param bool $reload
     * @return Service_Form|null
     */
    public function getService($reload = false)
    {
        if (is_null($this->service) || $reload) {
            $this->service = Service_Form::forge($this);
        }
        return $this->service;
    }

    /**
     * Gets the answers count
     *
     * @return int
     */
    public function getAnswersCount()
    {
        return $this->is_new() ? 0 : (int) Model_Answer::count(array(
            'where' => array(
                array('answer_form_id' => $this->form_id),
            ),
        ));
    }

    /**
     * @param $targetContext : the context target wanted for the duplicated form
     * @throws \Exception
     */
    public function duplicate($targetContext)
    {
        $clone = clone $this;
        $try = 1;
        do {
            try {
                $title_append = strtr(__(' (copy {{count}})'), array(
                    '{{count}}' => $try,
                ));
                $clone->form_virtual_name = null;
                $clone->form_name = $this->title_item().$title_append;
                $clone->form_context = $targetContext;
                if ($clone->save()) {
                    static::duplicateFormFields($this, $clone);
                }
                break;
            } catch (\Nos\BehaviourDuplicateException $e) {
                $try++;
                if ($try > 5) {
                    throw new \Exception(__(
                        'Slow down, slow down. You have duplicated this form 5 times already. '.
                        'Edit them first before creating more duplicates.'
                    ));
                }
            }
        } while ($try <= 5);
    }

    /**
     * @param Model_Form $form : The original form, fields will duplicated FROM
     * @param Model_Form $duplicatedForm : The duplicated form, fields will duplicated TO
     */
    protected static function duplicateFormFields(Model_Form $form, Model_Form $duplicatedForm)
    {
        $formFields = $form->fields;
        $arrFieldsIds = array();
        foreach ($formFields as $field) {
            /**
             * @var $field Model_Field
             */
            $clone = clone $field;
            $clone->field_form_id = $duplicatedForm->id;
            if ($clone->save()) {
                $arrFieldsIds[$field->id] = $clone->id;
                static::duplicateFieldsAttributes($field, $clone);
            }
        }
        // Refresh the new form layout with new fields IDs
        static::updateDuplicatedFormLayout($arrFieldsIds, $duplicatedForm);
    }

    /**
     * @param $arrFieldsIds : array of new fields IDs with old field id keys
     * @param Model_Form $duplicatedForm : the duplicated form to apply its new form_layout
     */
    protected static function updateDuplicatedFormLayout($arrFieldsIds, Model_Form $duplicatedForm)
    {
        $formLayout = explode("\n", $duplicatedForm->form_layout);
        $newFormLayout = array();
        foreach ($formLayout as $rowLayout) {
            $arrColumns = explode(',', $rowLayout);
            $arrNewColumns = array();
            foreach ($arrColumns as $column) {
                $columnDetail = explode('=', $column);
                $fieldId = (int)array_shift($columnDetail);
                if (array_key_exists($fieldId, $arrFieldsIds)) {
                    array_unshift($columnDetail, \Arr::get($arrFieldsIds, $fieldId));
                    $arrNewColumns[] = implode('=', $columnDetail);
                } else {
                    $arrNewColumns[] = $column;
                }
            }
            $newFormLayout[] = implode(',', $arrNewColumns);
        }
        $duplicatedForm->form_layout = implode("\n", $newFormLayout);
        $duplicatedForm->save();
    }

    /**
     * @param Model_Field $field : The original field, attributes will duplicated FROM
     * @param Model_Field $duplicatedField : The duplicated field, attributes will duplicated TO
     */
    protected static function duplicateFieldsAttributes(Model_Field $field, Model_Field $duplicatedField)
    {
        $fieldsAttributes = $field->attributes;
        foreach ($fieldsAttributes as $attribute) {
            /**
             * @var $attribute Model_Field_Attribute
             */
            $clone = clone $attribute;
            $clone->fiat_field_id = $duplicatedField->id;
            $clone->save();
        }
    }

    /**
     * Before item's deletion
     */
    public function _event_before_delete()
    {
        $this->_form_id_for_delete = $this->form_id;
    }

    /**
     * After item's deletion
     */
    public function _event_after_delete()
    {
        if (is_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete)) {
            \Fuel\Core\File::delete_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete);
        }

        \Nos\Attachment::deleteAlias('form/'.$this->_form_id_for_delete);
    }
}
