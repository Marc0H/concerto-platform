<?php
if (!isset($ini))
{
    require_once'../../Ini.php';
    $ini = new Ini();
}
$logged_user = User::get_logged_user();
if ($logged_user == null) 
{
    echo "<script>location.reload();</script>";
    die(Language::string(278));
}

if (isset($oid))
{
    if(!$logged_user->is_module_writeable($class_name)) die(Language::string(81));
    
    $parameters = $obj->get_parameter_CustomSectionVariables();
    $returns = $obj->get_return_CustomSectionVariables();
    $code = $obj->code;
}
else
{
    if(!$logged_user->is_module_writeable($_POST['class_name'])) die(Language::string(81));
    
    $oid = $_POST['oid'];
    $obj = CustomSection::from_mysql_id($oid);
    
    if(!$logged_user->is_object_editable($obj)) die(Language::string(81));
    
    $parameters = array();
    if (array_key_exists("parameters", $_POST))
    {
        foreach ($_POST['parameters'] as $par)
        {
            array_push($parameters, json_decode($par));
        }
    }
    $returns = array();
    if (array_key_exists("returns", $_POST))
    {
        foreach ($_POST['returns'] as $ret)
        {
            array_push($returns, json_decode($ret));
        }
    }
    $code = $_POST['code'];
    $class_name = $_POST['class_name'];
}
?>

<script>
    $(function(){
        Methods.iniCodeMirror("form<?= $class_name ?>TextareaCode", "r", false);
        Methods.iniTooltips();
        CustomSection.uiRefreshComboboxes();
    
        $(".tooltipCustomSectionLogic").tooltip({
            content:function(){
                return "<?= Language::string(104) ?><hr/>"+$(this).next().val();
            }
        });
    });
</script>

<table>
    <tr>
        <td><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= Language::string(105) ?>"></span></td>
        <td><b><?= Language::string(106) ?>:</b></td>
    </tr>
</table>
<div class="ui-widget-content ui-state-focus">
    <div class="div<?= $class_name ?>Parameters">
        <?php
        foreach ($parameters as $param)
        {
            ?>
            <div>
                <table>
                    <tr>
                        <td>
                            <input onchange="CustomSection.uiVarNameChanged($(this))" type="text" class="ui-state-focus comboboxCustomSectionVars comboboxCustomSectionVarsParameter ui-widget-content ui-corner-all" value="<?= htmlspecialchars($param->name, ENT_QUOTES) ?>" />
                        </td>
                        <td>
                            <span class="spanIcon tooltipCustomSectionLogic ui-icon ui-icon-document-b" onclick="CustomSection.uiEditVariableDescription($(this).next())" title="<?= Language::string(107) ?>"></span>
                            <textarea class="notVisible"><?= $param->description ?></textarea>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
        }
        if (count($parameters) == 0)
        {
            ?>
            <div class="ui-state-error padding margin" align="center"><?= Language::string(108) ?></div>
            <?php
        }
        ?>
    </div>
    <div class="notVisible">
        <?php
        foreach ($parameters as $param)
        {
            ?>
            <input class="inputCustomSectionParameterVar" type="hidden" value="<?= $param->name ?>" />
            <?php
        }
        ?>
    </div>
    <table>
        <tr>
            <td><span class="spanIcon tooltip ui-icon ui-icon-plus" onclick="CustomSection.uiAddParameter()" title="<?=Language::string(109)?>"></span></td>
            <td><?php if (count($parameters) > 0)
        {
            ?><span class="spanIcon tooltip ui-icon ui-icon-minus" onclick="CustomSection.uiRemoveParameter()" title="<?=Language::string(110)?>"></span><?php } ?></td>
        </tr>
    </table>
</div>
<br/>

<table>
    <tr>
        <td><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?=Language::string(111)?>"></span></td>
        <td><b><?=Language::string(49)?>:</b></td>
    </tr>
</table>
<textarea id="form<?= $class_name ?>TextareaCode" class="fullWidth ui-widget-content ui-corner-all textareaCode"><?= $code ?></textarea>

<br/>
<table>
    <tr>
        <td><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?=Language::string(112)?>"></span></td>
        <td><b><?=Language::string(113)?>:</b></td>
    </tr>
</table>
<div class="ui-widget-content ui-state-focus">
    <div class="div<?= $class_name ?>Returns">
        <?php
        foreach ($returns as $ret)
        {
            ?>
            <div>
                <table>
                    <tr>
                        <td>
                            <input onchange="CustomSection.uiVarNameChanged($(this))" type="text" class="ui-state-focus comboboxCustomSectionVars comboboxCustomSectionVarsReturn ui-widget-content ui-corner-all" value="<?= htmlspecialchars($ret->name, ENT_QUOTES) ?>" />
                        </td>
                        <td>
                            <span class="spanIcon tooltipCustomSectionLogic ui-icon ui-icon-document-b" onclick="CustomSection.uiEditVariableDescription($(this).next())" title="<?=Language::string(107)?>"></span>
                            <textarea class="notVisible"><?= $ret->description ?></textarea>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
        }
        if (count($returns) == 0)
        {
            ?>
            <div class="ui-state-error padding margin" align="center"><?=Language::string(114)?></div>
            <?php
        }
        ?>
    </div>
    <div class="notVisible">
        <?php
        foreach ($returns as $ret)
        {
            ?>
            <input class="inputCustomSectionReturnVar" type="hidden" value="<?= $ret->name ?>" />
            <?php
        }
        ?>
    </div>
    <table>
        <tr>
            <td><span class="spanIcon tooltip ui-icon ui-icon-plus" onclick="CustomSection.uiAddReturn()" title="<?=Language::string(115)?>"></span></td>
            <td><?php if (count($returns) > 0)
        {
            ?><span class="spanIcon tooltip ui-icon ui-icon-minus" onclick="CustomSection.uiRemoveReturn()" title="<?=Language::string(116)?>"></span><?php } ?></td>
        </tr>
    </table>
</div>