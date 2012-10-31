
Columns of the nos_form_field table
===================================

 * field_id : primary key.
 * field_form_id : which form does the field belongs to. Can be 0 when the field is added but not saved in the form yet.
 * field_type : Type like "text", "radio", or "file". See config file for all possible values.
 * field_label : Field label for most fields
 * field_message : Field label & content when type="message"
 * field_virtual_name : uniqid to identify the field
 * field_choices : list of choices when type=select,radio,checkbox (1 choice per line)
 * field_created_at : observer
 * field_mandatory : required or not (0 or 1)
 * field_default_value : default selection (choice)
 * field_details : textual description for this field
 * field_style enum('p','h1','h2','h3') : when type=message
 * field_width : used when type=textual (text,email,number,...)
 * field_height : used when type=textarea
 * field_limited_to : used when type=textual (text,email,number,...)
 * field_origin : used when type=hidden,variable
 * field_origin_var : used when type=hidden,variable
 * field_technical_id : HTML ID attribute
 * field_technical_css : HTML class attribute


Type list
=========

 * text: Single line text
 * textarea: Paragraph text
 * checkbox: Checkboxes
 * select: Dropdown
 * radio: Multiple choices
 * file: File
 * email: Email (textual)
 * number: Number (textual)
 * date: Date
 * message: Message (displays text)
 * hidden: Hidden field
 * separator: Separator
 * variable: Variable (configured like an hidden field, but the content is displayed)


 
