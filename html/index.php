<!DOCTYPE html>
<html lang="pl">
<head>
  <title>SN32WOSP</title>
  <!-- Meta Tags for Bootstrap 5 -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
  <script src="script.js"></script>
</head>
<body>
  <?php
    require "functions.php";
    if(! isDev()) error_reporting(0);

    if(isDev()) print("<div><h1>dev</h1></div>");

    if(isset($_POST['callsign'])) {
      require "config.php";

      $callsign = strtoupper(htmlspecialchars(trim($_POST['callsign'])));
      $cert_condition_met = false;

      $apiCallResult = getWebPage($apiUrl . "?stationId=10&call=" . $callsign);

      $apiCallResultJson = json_decode($apiCallResult, true);
      
      debug_to_console($apiCallResult);

      $operator_points = countPoints(removeDuplicateMarkedQsos($apiCallResultJson));
      $operator_name   = getOperatorName($apiCallResultJson);

      if ($operator_points >= 2) {
        $cert_condition_met = true;
      }

      $is_payment = false;

      if($cert_condition_met) {
        if(isset($_POST['payment']) && $_POST['payment'] == "yes") {
          $is_payment = true;
        }
        $db_query = sprintf("insert into sn32wosp_downloads (id, date, callsign, payment) values (null, now(), \"%s\", %s);", $callsign, $is_payment ? 'true' : 'false');
        $db_result = mysqli_query($mysqli, $db_query);
	debug_to_console($db_result);

        $png_img = imagecreatefrompng('sn32wosp_template_cert.png');
        $color = imagecolorallocate($png_img, 0, 0, 0);
        $font_path = realpath("Arimo-Bold.ttf");
        $text_line1 = $operator_name;
        $text_line2 = $callsign;
        $cert_fn = "certs/" . $callsign . ".png";

        $imgx = imagesx($png_img);
        $imgy = imagesy($png_img);

        $font_size_line1 = 90;
        $font_size_line2 = 120;

        //determine the X position of text to be centered
        if($text_line1) $bbox_line1 = imagettfbbox($font_size_line1, 0, $font_path, $text_line1);
        $bbox_line2 = imagettfbbox($font_size_line2, 0, $font_path, $text_line2);
        if($text_line1) $center_line1 = intval(($imgx / 2) - (($bbox_line1[2] - $bbox_line1[0]) / 2));
        $center_line2 = intval(($imgx / 2) - (($bbox_line2[2] - $bbox_line2[0]) / 2));

        $text2_y = 910;
        if($text_line1) imagettftext($png_img, $font_size_line1, 0, $center_line1, 740, $color, $font_path, $text_line1);
        else $text2_y = 825;
        imagettftext($png_img, $font_size_line2, 0, $center_line2, $text2_y, $color, $font_path, $text_line2);

        if(isDev()) imagettftext($png_img, 200, 0, 600, 300, $color, $font_path, "DEV  DEV  DEV");

        imagepng($png_img, $cert_fn);
        imagedestroy($png_img);
      }
  ?>
  <div id="form">
    <button class="btn btn-secondary" onclick="history.back()">Powrót</button>
  <?php
      if($cert_condition_met) {
        print("  <button class=\"btn btn-primary\" type=\"submit\" onclick=\"downloadFile('" . $cert_fn . "')\">Pobierz</button>");
      }
  ?>

  </div>
  <?php
      if ($cert_condition_met) {
        if(! $is_payment) {
          print("<div><p class=\"info\">Masz jeszcze szansę wpłacić dowolną kwotę do wirtualnej puszki WOŚP - ZHP Wrocław Wschód <a href=\"https://eskarbonka.wosp.org.pl/sulomyliwa\" target=\"_blank\">tutaj</a>.</p></div>");
        }
        else {
          print("<div><p class=\"info\">Dziękujemy za wpłatę!</p></div>");
        }
        print("<div><p class=\"info\">Uzyskałeś punktów: " . $operator_points . "</p></div>");
        print("<div id=\"cert\"><img id=\"cert\" src=\"" . $cert_fn . "\" width=\"" . intval($imgx/3) . "\" height=\"" . intval($imgy/3) . "\" alt=\"Dyplom dla " . $callsign . "\" /></div>");
      }
      else {
        if($operator_points) {
          print("<div><p class=\"info\">Przepraszamy. Stacja " . $callsign . " nie uzyskała wymaganej liczby punktów (" . $operator_points . "/2).</div>");
        }
        else {
          print("<div><p class=\"info\">Przepraszamy. Stacja " . $callsign . " nie znajduje się w logu SN32WOSP.</div>");
        }
      }
    }
    else {
  ?>
  <div id="form">
    <form class="form-inline" method="post">
    Wpisz znak wywoławczy, następnie kliknij Sprawdź
      <div class="input-group">
        <input type="text" name="callsign" class="form-control" id="callsign" placeholder="Znak wywoławczy">
        <button type="submit" class="btn btn-primary">Sprawdź</button>
      </div>
      <div class="input-group">
        Czy wpłaciłeś dowolną kwotę do wirtualnej puszki WOŚP - ZHP Wrocław Wschód
        <select class=payment name="payment" required>
          <option value="" selected>--</option>
          <option value="yes">Tak</option>
          <option value="no">Nie</option>
        </select>
      </div>
      <div>
        <p class=small>Odpowiedź nie ma wpływu na otrzymanie dyplomu.</p>
      </div>
    </form>
    <div>
      <p>Jeszcze możesz wpłacić: <a href="https://eskarbonka.wosp.org.pl/sulomyliwa" target="_blank">https://eskarbonka.wosp.org.pl/sulomyliwa</a></p>
    </div>
  <?php
    }
  ?>
  </div>
</body>
</html>
