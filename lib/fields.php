<?php
define('NOT_PROVIDED', null);

class Field
{
    var $value = null;
    var $fieldtype = null;
    var $attributes = [];
    var $required_attributes = [];
    var $description = 'Field';
    var $empty_strings_allowed = true;
    var $default_error_messages = ['invalid' => 'Invalid Data Type'];
    var $_verbose_name, $_unique;
    # Attributes
    var $auto_created = false, $auto_increment = false, $blank = false, $choices = null, $db_column = null,
        $db_index = false, $db_tablespace = null, $default = NOT_PROVIDED, $editable = true, $error_messages = null,
        $help_text = '', $max_length = null, $unique = false, $name = null, $null = false, $primary_key = false,
        $rel = null, $serialize = true, $unique_for_date = null, $unique_for_month = null, $unique_for_year = null,
        $validators = [], $verbose_name = null;

    function __construct($fieldtype, $attributes = [])
    {
        $this->name = 
        $this->fieldtype = $fieldtype;
        $this->attributes = $attributes;

        $required_attribs = $this->_check_required_attribs();
        if ($required_attribs !== true) {
            $error_message = 'Missing Required Attribute(s)' . implode(",", $required_attribs);
            user_error($error_message, E_USER_ERROR);
        }

        foreach ($attributes as $key => $val) {
            $this[$key] = $val;
        }

        if ($this->max_length !== null) {
            $sqltype = "$this->fieldtype ($this->max_length)";
        } else {
            $sqltype = $this->fieldtype;
        }
        if (!$this->null) {
            $sqltype .= ' NOT NULL';
        }
        if ($this->unique) {
            $sqltype .= ' UNIQUE';
        }
        if ($this->auto_increment) {
            $sqltype .= "auto_increment";
        }
        if ($this->primary_key) {
            $sqltype .= ", KEY($this->name)";
        }
    }

    function _check_null($kwargs = [])
    {
        if ($this == 'null') {
            return [
                'error' => _('This field does not accept null values.'),
                'hint' => null,
                'obj' => $this,
                'id' => 'fields.E110'
            ];
        } else {
            return [];
        }
    }

    function _check_primary_key()
    {
        if (!$this->primary_key) {
            return [
                'error' => _('AutoFields must set primary_key=True.'),
                'hint' => null,
                'obj' => $this,
                'id' => 'fields.E100'
            ];
        } else {
            return [];
        }
    }

    function _check_required_attribs()
    {
        $missing_attributes = [];
        foreach ($this->required_attributes as $key) {
            if (!isset($this->attributes[$key]) || empty($this->attributes[$key])) {
                $missing_attributes[] = $key;
            }
        }
        return ($missing_attributes > 0 ? $missing_attributes : true);
    }

    function get_internal_type()
    {
        return get_class();
    }

    function __toString()
    {
        $specialtypes = [
            'null' => null,
            'true' => true,
            'false' => false
        ];
        if (in_array($specialtypes, $this->value)) {
            return strval(array_search($specialtypes, $this->value));
        } else {
            return strval($this->value);
        }
    }
}

class CharField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'varchar';
        $this->required_attributes = ['max_length'];
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class FixedCharField extends Field
{
    function __construct(array $attributes = [])
    {
        $this->fieldtype = 'char';
        $this->required_attributes = ['max_length'];
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class TextField extends Field
{
    function __construct(array $attributes = [])
    {
        $this->fieldtype = 'longtext';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class DateTimeField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'datetime';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class IntegerField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'int';
        $this->required_attributes = ['max_length'];
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class BooleanField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'bool';
        $this->attributes = $attributes;

        $this->empty_strings_allowed = false;
        $this->default_error_messages = [
            'invalid' => _("'%(value)s' value must be either True or False.")
        ];
        $this->description = _("Boolean (Either True or False)");

        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class FloatField extends Field
{
    var $max_digits = 53;
    var $decimal_places = 253;

    function __construct($attributes = [])
    {
        $this->fieldtype = 'real';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class DoubleField extends Field
{
    var $max_digits = 53;
    var $decimal_places = 253;

    function __construct($attributes = [])
    {
        $this->fieldtype = 'double';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class BigIntegerField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'bigint';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class SmallIntegerField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'smallint';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class DecimalField extends Field
{
    var $max_digits = 53;
    var $decimal_places = 253;

    function __construct($attributes = [])
    {
        $this->fieldtype = 'numeric';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class AutoField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'integer';
        $this->description = _(ucfirst($this->fieldtype));

        $this->default_error_messages = [
            'invalid' => _("'%(value)s' value must be an integer."),
        ];

        $this->empty_strings_allowed = false;
        $this->attributes['blank'] = false;
        $this->attributes['unique'] = true;

        $this->required_attributes = ['blank', 'unique'];

        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class BigAutoField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'bigint';
        $this->description = _(ucfirst($this->fieldtype));

        $this->default_error_messages = [
            'invalid' => _("'%(value)s' value must be an integer."),
        ];

        $this->empty_strings_allowed = false;
        $this->attributes['blank'] = false;
        $this->attributes['unique'] = true;

        $this->required_attributes = ['blank', 'unique'];

        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class_alias('AutoField', 'PrimaryKeyField');
class_alias('BigAutoField', 'BigPrimaryKeyField');

class ForeignKey extends Field
{
    /*
     * TODO: Get primary key from $related model and generate field names.
     */
    var $on_delete = 'CASCADE';

    function __construct($related_model, $attributes = [])
    {
        $this->fieldtype = 'bigint';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class_alias('ForeignKey', 'ForeignKeyField');

class DateField extends Field
{
    var $auto_now = false;
    var $auto_now_create = false;

    function __construct($attributes = [])
    {
        $this->fieldtype = 'date';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class TimeField extends Field
{
    var $auto_now = false;
    var $auto_now_create = false;

    function __construct($attributes = [])
    {
        $this->fieldtype = 'time';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class TimestampField extends Field
{
    var $auto_now = false;
    var $auto_now_create = false;

    function __construct($attributes = [])
    {
        $this->fieldtype = 'int';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class BlobField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'blob';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class BinaryField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'binary';
        parent::__construct($this->fieldtype, $this->attributes);
    }
}

class UUIDField extends Field
{
    function __construct($attributes = [])
    {
        $this->fieldtype = 'varchar';
        $attributes['max_length'] = 32;
        $this->required_attributes = ['max_length'];
        parent::__construct($this->fieldtype, $this->attributes);
    }
}