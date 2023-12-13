<?php

namespace GWP;

abstract class HTMLGenerator {

	################################### Buttons ###################################################
	public static function createSubmitButton(string $id, string $name, string $text, string $controller, string $action, array $parameter = []): string {
		$formaction = "?c=$controller&a=$action";
		$parameterString = self::createParameterForUrl($parameter);
		return "<button formaction=\"{$formaction}{$parameterString}\" type=\"submit\" id=\"$id\" name=\"$name\">$text</button>";
	}

	public static function createLink(string $text, string $controller, string $action, string $title, array $parameter = [], string $onclick = '', string $target = '_self'): string {
		$parameterString = self::createParameterForUrl($parameter);
		return "<a title=\"$title\" href=\"?c=$controller&a=$action$parameterString\" onclick=\"$onclick\" target=\"$target\">$text</a>";
	}

	public static function createLinkWithButton(string $text, string $controller, string $action, array $parameter = [], string $onclick = ''): string {
		$parameterString = self::createParameterForUrl($parameter);
		return "<a href=\"?c=$controller&a=$action$parameterString\" onclick=\"$onclick\"><button type=\"button\">$text</button></a>";
	}

	public static function createLinkWithURL(string $text, string $link): string {
		return "<a href=\"$link\" target=\"_blank\">$text</a>";
	}

  public static function createButton(string $text, string $onclick = '', ?string $id = null): string {
    $idString = '';
    if($id != null) {
      $idString = " id='$id'";
    }
    return "<button{$idString} type='button' onclick='$onclick'>$text</button>";
  }

	################################### Fields ###################################################
	public static function getInputField(string $id, string $name, string $value, string $type = 'text', bool $isRequired = false, string $placeholder = '', string $lable = '', bool $isDisabled = false): string {
    $required = '';
    if($isRequired) {
      $required = 'required';
    }

    $disabled = '';
    if($isDisabled) {
      $disabled = 'disabled';
    }

    return "$lable<input $disabled type=\"$type\" id=\"$id\" name=\"$name\" value=\"$value\" placeholder=\"$placeholder\" $required>";
  }

  public static function getTextarea(string $id, string $name, string $value, string $lable = '', bool $isDisabled = false, string $height = '100px'): string {
    $disabled = '';
    if($isDisabled) {
      $disabled = 'disabled';
    }

    return "$lable<textarea style=\"height: $height;\" $disabled id=\"$id\" name=\"$name\">$value</textarea>";
  }

  public static function getInputFieldWithDropdown(string $id, string $name, string $value, array $dropdownData, string $type = 'text', bool $isRequired = false, string $placeholder = '', string $lable = '', string $onchange = ''): string {
    $required = '';
    if($isRequired) {
      $required = 'required';
    }


    $datalist = "<datalist id=\"{$name}List\">";
    foreach ($dropdownData as $entry) {
      $datalist .= "<option value=\"$entry\">";
    }
    $datalist .= '</datalist>';

    $html = "$lable<input list=\"{$name}List\" type=\"$type\" id=\"$id\" name=\"$name\" value=\"$value\" placeholder=\"$placeholder\" $required onchange=\"$onchange\">";
    $html .= $datalist;
    return $html;
  }

	public static function getSelect(string $id, string $name, array $data, string $selectedValue, bool $allowEmpty, string $lable = ''): string {
		$html = "$lable<select id=\"$id\" name=\"$name\">";

    if($allowEmpty) {
      $html .= '<option></option>';
    }

		foreach ($data as $key => $value) {
      $selected = '';
      if($key == $selectedValue) {
        $selected = 'selected';
      }
			$html .= "<option value=\"$key\" $selected>$value</option>";
		}
		$html .= "</select>";

		return $html;
	}

	public static function getCheckbox(string $id, string $name, string $lable = '', string $checked = '', string $onclick = '', string $selector = ''): string {
		return "<div class=\"checkBox\"><input $selector type=\"checkbox\" id=\"$id\" name=\"$name\" $checked onclick=\"$onclick\">$lable</div>";
	}

	################################### Other HTML ###################################################
	public static function getLabel(string $for, string $text): string {
		return "<label for=\"$for\"><b>$text</b></label>";
	}

  /**
   * Diese Funktion erzeugt einen Swal Pop Up in dem ein QR Code erzeugt und angezeigt wird.
   *
   * @param string $data - Data for in QRCode
   * @param string $qrCodeText - Text for under the QRCode
   * @param string $pictureName - Name des Bildes aus dem Ordner pageImages
   * @return string - HTML
   */
  public static function getQRCodeSwal(string $data): string {
    $data = urlencode($data);

    $swal = "Swal.fire({
              title: 'QR-Code bitte scannen! NICHT abfotografieren!',
              imageUrl: '?c=Shared&a=qrCode&data=$data',
              imageWidth: 600,
              imageHeight: 600,
              imageAlt: 'QR-Code',
              customClass: 'swal-image-size'
            });";

    return "<i class=\"fa-solid fa-qrcode qrcodeButton\" style=\"font-size: 40px\" onclick=\"$swal\"></i>";
  }

	/**
	 * Im HTML muss der Platzhalter folgendermaßen definiert sein:
	 * <!-- BEGIN error -->
	 *  <div class="error">
	 *    {errors}
	 *  </div>
	 * <!-- END error -->
	 *
	 * @param \HTML_Template_IT &$it
	 * @param array $errors - Array für Fehlermeldungen
	 *
	 * @return void
	 */
  public static function fillErrorMessages(\HTML_Template_IT &$it, array $errors): void {
    if(!empty($errors)) {
      $it->setCurrentBlock('error');

      $errorPlaceholder = [
        'errors' => implode('<br>', $errors)
      ];

      $it->setVariable($errorPlaceholder);
      $it->parseCurrentBlock();
    }
  }

  /**
   * @param \HTML_Template_IT $it
   * @param string $title
   * @param string $message
   * @param string $icon
   * @param array $parameter
   * @return void
   */
  public static function setSweetAlertNotification(\HTML_Template_IT &$it, array $sweetAlertConfig): void {
    $it->setCurrentBlock('sweetAlert');
    $it->setVariable([
      'sweetAlert' => 'swal.fire(' . json_encode($sweetAlertConfig) . ');'
    ]);
    $it->parseCurrentBlock();
  }

  /**
   * Erzeugt das HTMl für einen Untermenüeintrag
   *
   * @param string $controller
   * @param string $action
   * @param string $parameter
   * @param string $name
   * @return string
   */
  public static function createSubMenuEntryHTML(string $controller, string $action, string $parameter, string $name): string {
    return "<a href=\"?c=$controller&a=$action$parameter\" class=\"subLink\"><nobr>$name</nobr></a>\n";
  }

  ################################### HTML TABS ###########################################
  /**
   * Erzeugt eine Leite mit Tabs und gibt das HTMl zurück
   *
   * @param array $tabNames - Name/Titel der Tabs welcher angezeigt wird
   * @return string - HTML
   */
  public static function createTabs(array $tabNames): string {
    $html = "<div class=\"tab\">";
    foreach($tabNames as $name) {
      $html .= "<button type='button' class=\"tablinks\" onclick=\"openTab(event, '$name')\">$name</button>";
    }
    $html .= '</div>';
    return $html;
  }

  /**
   * Formatiert den Inhalt des Tabs für die Darstellung
   *
   * @param string $id - ID des Tabs
   * @param string $content - Inhalt des Tabs
   * @return string - HTML
   */
  public static function createTabContent(string $id, string $content, bool $activeOnDefault = false): string {
    $display = ($activeOnDefault ? 'block' : 'none');
    return "<div id=\"$id\" class=\"tabcontent\" style=\"display: $display\">
             $content
            </div>";
  }

	################################### PRIVATE ###########################################
	private static function createParameterForUrl(array $parameter): string {
		$parameterString = '';
		foreach($parameter as $key => $value) {
			if($value == null) {
				$parameterString .= "&$key";
			} else {
				$parameterString .= "&$key=$value";
			}
		}
		return $parameterString;
	}
}