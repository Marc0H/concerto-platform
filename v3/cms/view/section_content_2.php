<?php
/*
  Concerto Platform - Online Adaptive Testing Platform
  Copyright (C) 2011-2012, The Psychometrics Centre, Cambridge University

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; version 2
  of the License, and not any of the later versions.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if (!isset($ini)) {
    require_once'../../Ini.php';
    $ini = new Ini();
}
$logged_user = User::get_logged_user();
if ($logged_user == null) {
    echo "<script>location.reload();</script>";
    die(Language::string(278));
}
?>
<script>
    $(function(){
        Methods.iniTooltips();
    })
</script>
<?php
$vals = $_POST['value'];
if (array_key_exists('oid', $_POST) && $_POST['oid'] != 0) {
    $section = TestSection::from_mysql_id($_POST['oid']);
    $vals = $section->get_values();
}

// 0 - html
// 1 - params_count
// 2 - returns_count
// vars

$description = Language::string(213);

$template = Template::from_mysql_id($vals[0]);
if ($template != null) {
    $description.=" " . Language::string(214) . ":<hr/>" . $template->get_description();
}
?>

<div class="divSectionSummary sortableHandle">
    <table class="fullWidth tableSectionHeader">
        <tr>
            <!--<td class="tdSectionColumnIcon"></td>-->
            <td class="ui-widget-header tdSectionColumnCounter" id="tooltipSectionDetail_<?= $_POST['counter'] ?>" title=""><?= $_POST['counter'] ?></td>
            <td class="tdSectionColumnIcon"><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= DS_TestSectionType::get_description_by_id(2) ?>"></span></td>
            <td class="tdSectionColumnIcon"><span id="spanExpandDetail_<?= $_POST['counter'] ?>" class="spanExpandDetail spanIcon ui-icon ui-icon-folder-<?= $_POST['detail'] == 1 ? "open" : "collapsed" ?> tooltip" title="<?= Language::string(390) ?>" onclick="Test.uiToggleDetails(<?= $_POST['counter'] ?>)"></span></td>
            <td class="tdSectionColumnType"><?= DS_TestSectionType::get_name_by_id(2) ?></td>
            <td class="tdSectionColumnAction">
                <table class="fullWidth">
                    <tr>
                        <td>
                            <span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= htmlspecialchars($description, ENT_QUOTES) ?>"></span>
                        </td>
                        <td class="fullWidth">
                            <select id="selectTemplate_<?= $_POST['counter'] ?>" class="fullWidth ui-widget-content ui-corner-all fullWidth" onchange="Test.uiRefreshSectionContent(<?= $_POST['type'] ?>, <?= $_POST['counter'] ?>, Test.getSectionValues(Test.sectionDivToObject($('#divSection_<?= $_POST['counter'] ?>'))))">
                                <option value="0">&lt;<?= Language::string(73) ?>&gt;</option>
                                <?php
                                $sql = $logged_user->mysql_list_rights_filter("Template", "`name` ASC");
                                $z = mysql_query($sql);
                                while ($r = mysql_fetch_array($z)) {
                                    $t = Template::from_mysql_id($r[0]);
                                    ?>
                                    <option value="<?= $t->id ?>" <?= ($vals[0] == $t->id ? "selected" : "") ?>><?= $t->name ?> ( <?= $t->get_system_data() ?> )</option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="tdSectionColumnIcon"></td>
            <td class="tdSectionColumnEnd"><input type="checkbox" class="tooltip" id="chkEndSection_<?= $_POST['counter'] ?>" class="chkEndSection" <?= $_POST['end'] == 1 ? "checked" : "" ?> title="<?= Language::string(369) ?>" /></td>
            <td class="tdSectionColumnIcon"><span class="spanIcon tooltip ui-icon ui-icon-trash" onclick="Test.uiRemoveSection(<?= $_POST['counter'] ?>)" title="<?= Language::string(59) ?>"></span></td>
            <td class="tdSectionColumnIcon"><span class="spanIcon tooltip ui-icon ui-icon-plus" onclick="Test.uiAddLogicSection(0,<?= $_POST['counter'] ?>)" title="<?= Language::string(60) ?>"></span></td>
        </tr>
    </table>
</div>
<div class="divSectionDetail <?= $_POST['detail'] == 1 || $_POST['oid'] == 0  ? "" : "notVisible" ?>">
    <?php
    if ($vals[0] != 0 && $template != null) {
        ?>
        <table class="fullWidth">
            <tr>
                <td style="width:50%;" valign="top" align="center">
                    <div class="ui-widget-content">
                    <div class="ui-widget-header" align="center"><?= Language::string(106) ?>:</div>
                        <div>
                            <table class="fullWidth">
                                <?php
                                $inserts = $template->get_inserts();

                                for ($i = 0; $i < count($inserts); $i++) {
                                    $is_special = false;
                                    if ($inserts[$i] == "TIME_LEFT")
                                        $is_special = true;

                                    if (!$is_special) {
                                        $val = $inserts[$i];
                                        for ($j = 0; $j < $vals[1] * 2; $j = $j + 2) {
                                            if ($vals[3 + $j] == $inserts[$i] && isset($vals[3 + $j + 1]) && $vals[3 + $j + 1] != "") {
                                                $val = $vals[3 + $j + 1];
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= Language::string(215) ?>"></span></td>
                                        <td><?= $inserts[$i] ?></td>
                                        <td><b><-</b></td>
                                        <td>
                                            <?php
                                            if (!$is_special) {
                                                ?>    
                                                <input type="text" referenced="<?= $inserts[$i] ?>" class="controlValue<?= $_POST['counter'] ?>_params ui-widget-content ui-corner-all comboboxVars fullWidth" value="<?= htmlspecialchars($val, ENT_QUOTES) ?>" />
                                                <?php
                                            }
                                            else
                                                echo "&nbsp;";
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= Language::string(216) ?>"></span></td>
                                    <td>TIME_LIMIT</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </div>
                        <div class="notVisible">
                            <?php
                            for ($i = 0; $i < count($inserts); $i++) {
                                ?>
                                <input class="inputParameterVar" type="hidden" value="<?= $inserts[$i] ?>" />
                                <?php
                            }
                            ?>
                            <input class="inputParameterVar" type="hidden" value="TIME_LIMIT" />
                        </div>
                    </div>
                </td>
                <td style="width:50%;" valign="top" align="center">
                    <div class="ui-widget-content">
                    <div class="ui-widget-header" align="center"><?= Language::string(113) ?>:</div>
                        <div>
                            <table class="fullWidth">
                                <?php
                                $outputs = $template->get_outputs();

                                for ($i = 0; $i < count($outputs); $i++) {
                                    $ret = $outputs[$i]["name"];

                                    for ($j = 0; $j < $vals[2] * 2; $j = $j + 2) {
                                        if ($vals[3 + $vals[1] * 2 + $j] == $outputs[$i]['name'] && isset($vals[3 + $vals[1] * 2 + $j]) && $vals[3 + $vals[1] * 2 + $j] != "") {
                                            $ret = $vals[3 + $vals[1] * 2 + $j + 1];
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= Language::string(217) ?>: <b><?= $outputs[$i]["type"] ?></b>"></span></td>
                                        <td><?= $outputs[$i]["name"] ?></td>
                                        <td><b>->></b></td>
                                        <td><input referenced="<?= $outputs[$i]["name"] ?>" onchange="Test.uiSetVarNameChanged($(this))" type="text" class="ui-state-focus comboboxSetVars comboboxVars controlValue<?= $_POST['counter'] ?>_rets ui-widget-content ui-corner-all fullWidth" value="<?= htmlspecialchars($ret, ENT_QUOTES) ?>" /></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= Language::string(283) ?>"></span></td>
                                    <td>LAST_PRESSED_BUTTON_NAME</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><span class="spanIcon ui-icon ui-icon-help tooltip" title="<?= Language::string(283) ?>"></span></td>
                                    <td>TIME_TAKEN</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table> 
                        </div>
                        <div class="notVisible">
                            <?php
                            for ($i = 0; $i < count($outputs); $i++) {
                                ?>
                                <input class="inputReturnVar" type="hidden" value="<?= $outputs[$i]['name'] ?>" />
                                <?php
                            }
                            ?>
                            <input class="inputReturnVar" type="hidden" value="LAST_PRESSED_BUTTON_NAME" />
                            <input class="inputReturnVar" type="hidden" value="TIME_TAKEN" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <?php
    }
    ?>
</div>