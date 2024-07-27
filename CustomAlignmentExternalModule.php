<?php namespace DE\RUB\CustomAlignmentExternalModule;

require_once "classes/ActionTagParser.php";

/**
 * ExternalModule class for Custom Alignment.
 */
class CustomAlignmentExternalModule extends \ExternalModules\AbstractExternalModule {

    const AT_CAF = "@CUSTOM-ALIGNMENT-FORM";
    const AT_CAS = "@CUSTOM-ALIGNMENT-SURVEY";
    const AT_CAP = "@CUSTOM-ALIGNMENT-PDF";

    #region Hooks

    function redcap_every_page_before_render ($project_id) {
        if ($project_id === null) return;

        $page = defined("PAGE") ? PAGE : "";
        if ($page == "DataEntry/index.php") {
            global $Proj;
            $form = isset($Proj->forms[$_GET["page"]]) ? $_GET["page"] : "";
            if ($form != "") {
                $this->set_custom_alignment($Proj, $form, self::AT_CAF);
            }
        }
        else if ($page == "surveys/index.php") {
            if (isset($_GET["s"])) {
                global $Proj;
                $context = \Survey::getSurveyContextFromSurveyHash($_GET["s"]);
                $this->set_custom_alignment($Proj, $context["form_name"], self::AT_CAS);
            }
        }
    }

    #endregion


    private function set_custom_alignment($Proj, $form, $at_name) {
        foreach ($Proj->forms[$form]["fields"] as $target => $_) {
            $meta = $Proj->metadata[$target] ?? [];
            $misc = $meta["misc"] ?? "";
            if (strpos($misc, $at_name) !== false) {
                $result = ActionTagParser::parse($misc);
                foreach ($result["parts"] as $at) {
                    if ($at["text"] == $at_name && $at["param"]["type"] == "quoted-string") {
                        $aligment = $this->parse_aligment($at["param"]["text"]);
                        if ($aligment) {
                            $Proj->metadata[$target]["custom_alignment"] = $aligment;
                        }
                    }
                }
            }
        }
    }

    private function parse_aligment($text) {
        // Unwarp quotes
        $text = substr($text, 1, -1);
        if (strpos(".LH.LV.RH.RV.", ".{$text}.") !== false) {
            return $text;
        }
        return null;
    }
}