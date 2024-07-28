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
            $form = isset($Proj->forms[$_GET["page"]]) ? $_GET["page"] : null;
            if ($form) {
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

    function redcap_pdf($project_id, $metadata, $data, $instrument, $record, $event_id, $instance) {
        if (!isset($GLOBALS["Proj"])) return;
        global $Proj;
        $this->set_custom_alignment($Proj, $instrument, self::AT_CAP);
        // Transfer to $metadata
        foreach ($metadata as &$m) {
            $m["custom_alignment"] = $Proj->metadata[$m["field_name"]]["custom_alignment"] ?? null;
        }
        return [ "metadata" => $metadata, "data" => $data ];
    }

    #endregion

    #region Implementation

    private function set_custom_alignment($Proj, $form, $at_name) {
        $fields = array_keys(array_key_exists($form, $Proj->forms) ? $Proj->forms[$form]["fields"] : $Proj->metadata);
        foreach ($fields as $field) {
            $meta = $Proj->metadata[$field] ?? [];
            $misc = $meta["misc"] ?? "";
            if (strpos($misc, $at_name) !== false) {
                $result = ActionTagParser::parse($misc);
                foreach ($result["parts"] as $at) {
                    if ($at["text"] == $at_name && $at["param"]["type"] == "quoted-string") {
                        $aligment = $this->parse_aligment($at["param"]["text"]);
                        if ($aligment) {
                            $Proj->metadata[$field]["custom_alignment"] = $aligment;
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

    #endregion
}