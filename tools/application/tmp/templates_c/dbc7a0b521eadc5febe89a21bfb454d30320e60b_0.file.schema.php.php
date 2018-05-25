<?php
/* Smarty version 3.1.31, created on 2018-01-17 11:45:03
  from "/Applications/MAMP/htdocs/iot_lambda/tools/application/views/schema_dump/schema.php" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5a5eb8afe61575_68621163',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dbc7a0b521eadc5febe89a21bfb454d30320e60b' => 
    array (
      0 => '/Applications/MAMP/htdocs/iot_lambda/tools/application/views/schema_dump/schema.php',
      1 => 1513652926,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a5eb8afe61575_68621163 (Smarty_Internal_Template $_smarty_tpl) {
echo '<?php ';?>if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config["database_schema"] = array(
    // このファイルは自動で生成されるファイルである。
    // データベースのスキーマを更新した場合は、こちらのファイルを生成し直すこと。
    // データベースのスキーマとこのファイルに差分がある状態で本番環境へ移行すると不具合の原因になるので注意すること。
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['schema']->value, 'database');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['database']->value) {
?>

    '<?php echo $_smarty_tpl->tpl_vars['database']->value['name'];?>
' => array(
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['database']->value['tables'], 'table');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['table']->value) {
?>

        '<?php echo $_smarty_tpl->tpl_vars['table']->value['name'];?>
' => array(
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['table']->value['columns'], 'column');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['column']->value) {
?>
            '<?php echo $_smarty_tpl->tpl_vars['column']->value['name'];?>
' => array('type' => '<?php echo $_smarty_tpl->tpl_vars['column']->value['type'];?>
', 'strict_type' => "<?php echo $_smarty_tpl->tpl_vars['column']->value['strict_type'];?>
", 'null' => <?php echo $_smarty_tpl->tpl_vars['column']->value['null'];?>
),
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

        ),
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    ),
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

);

<?php }
}
