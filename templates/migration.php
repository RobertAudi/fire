<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * {{class_name}}
 */
class {{class_name}} extends {{parent_class}}
{
    public function up()
    {
        $this->dbforge->add_field(array(
{{extra}}
        ));

        $this->dbforge->create_table('{{table_name}}');
    }

    public function down()
    {
        $this->dbforge->drop_table('{{table_name}}');
    }
}

/* End of file {{filename}} */
/* Location {{application_folder}}/controllers/{{filename}} */
