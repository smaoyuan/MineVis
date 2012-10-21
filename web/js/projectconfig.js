/*
 * Update the field selectors when tables are selected
 */
var tableA = "";
var tableB = "";
var project_id = -1;
var loading = {
    '': 'loading...'
};
var cache = {};
var smart_names = ['name','title','text'];

/*
 * Declare Form Events
 */
$(document).ready(function()
{
    project_id = $('#project_config_project_id').val();
    /*
     * Table A selected
     */
    $('#project_config_table_a').change(function() {
        removeOption('project_config_table_a', '');
        loadTableAFields(this.id);
    });

    /*
     * Table B selected
     */
    $('#project_config_table_b').change(function() {
        removeOption('project_config_table_b', '');
        loadTableBFields(this.id);
    });

    /*
     * Table AxB selected
     */
    $('#project_config_table_axb').change(function() {
        removeOption('project_config_table_axb', '');
        loadTableAxBFields(this.id);
    });
});

/**
 * Get the table fields from ajax (or cache)
 * @param tableName name of table to get fields for
 * @param select_ids id of the select to update options for
 * @param selections selection for automatically picking fields.
 * @return Array of options of each field
 */
function setOptionFields(select_ids, tableName, selections) {
    if (tableName in cache) {
        $.each(select_ids, function(key, val) {
            changeOptions(val, cache[ tableName ]);
            smartSelectOption(val,selections[key]);
        });
    } else {
        if (project_id >= 0) {
            var request = {
                'table': tableName,
                'project_id': project_id
            };
            $.getJSON('fields.json', request, function(data) {
                cache[ tableName ] = data;
                $.each(select_ids, function(key, val) {
                    changeOptions(val, data);
                    smartSelectOption(val,selections[key]);
                });
                smartAxBSelect();
            });
        }
    }
}

/*
 * Load Table A fields
 * This file loads the ajax content into table A fields.
 * 1. displays loading.
 * 2. get data
 * 3. try to smartly select the right option...
 */
function loadTableAFields(id) {
    //feed back just it case it takes a while
    changeOptions('project_config_table_a_id_field',loading);
    changeOptions('project_config_table_a_description_field',loading);

    tableA = $('#' + id).val();

    preferred_options = [];
    preferred_options[0] = ['id'];
    preferred_options[1] = smart_names;

    //ajax in the data
    setOptionFields(['project_config_table_a_id_field',
        'project_config_table_a_description_field'], tableA, preferred_options);
}

/*
 * Load Table B fields same as table A but with other data and for table B
 * This file loads the ajax content into table B fields.
 * 1. displays loading.
 * 2. get data
 * 3. try to smartly select the right option...
 */
function loadTableBFields(id) {
    //feed back just it case it takes a while
    changeOptions('project_config_table_b_id_field',loading);
    changeOptions('project_config_table_b_description_field',loading);

    tableB = $('#' + id).val();

    preferred_options = [];
    preferred_options[0] = ['id'];
    preferred_options[1] = smart_names;

    //ajax in the data
    setOptionFields(['project_config_table_b_id_field',
        'project_config_table_b_description_field'], tableB,preferred_options);
}

/*
 * Once again doing the same thing except for the center table.
 * This time when doing smart select we try to use the names of the other 2 tables...
 */
function loadTableAxBFields(id) {
    var tableAxB = $('#' + id).val();
    //feed back just it case it takes a while
    changeOptions('project_config_table_axb_table_a_id_field',loading);
    changeOptions('project_config_table_axb_table_b_id_field',loading);

    preferred_options = [];
    preferred_options[0] = [tableA+'_id',tableA.substring(0,3)+'_id'];
    preferred_options[1] = [tableB+'_id',tableB.substring(0,3)+'_id'];

    //ajax in the data
    setOptionFields(['project_config_table_axb_table_a_id_field',
        'project_config_table_axb_table_b_id_field'], tableAxB, preferred_options);
}

/**
 * Helper function
 * This adds the values in the array as options in the select.
 */
function changeOptions(id, data) {
    var options = '';
    $.each(data, function(key, value) {
        options += '<option value="'+key+'">'+value+'</option>\n';
    });
    $('#'+id).html(options);
}

/**
 * Helper function
 * This clears the options from a select. it's used as a way to reset it
 * before adding new values.
 */
function removeOption(id, value) {
    $('#'+id+' option[value="'+value+'"]').remove();
}

/**
 * This is the select smart helper,
 * takes a select id and an array of target values in prioritized order,
 * tries to select the first value,
 * if it can done else try on the next one and so on.
 *
 */
function smartSelectOption(id, valuearray) {
    found = false;
    $.each (valuearray, function(key,target) {
        if (!found) {
            selectOption(id, target);
            if (getSelected(id) == target) {
                found = true;
            }
        }
    });
    return found;
}

function smartAxBSelect() {
    if (tableA != "" && tableB != "") {
        preferred_options = [tableA + "_" + tableB, tableB + "_" + tableA];
        if (smartSelectOption('project_config_table_axb', preferred_options)) {
            loadTableAxBFields('project_config_table_axb');
        }
    }
}

/**
 * Helper function
 * Sets the selected value if exists in a select
 */
function selectOption(id, value) {
    $('#'+id+' option[value="'+value+'"]').attr('selected','selected');
}

/**
 * Helper function
 * Gets the selected value from a select
 */
function getSelected(id) {
    return $('#'+id+' option:selected').text();
}