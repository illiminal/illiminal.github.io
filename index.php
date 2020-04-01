<?php
$getToday = date("n/j/Y", strtotime("now"));
$getOne = date("n/j/Y", strtotime("+1 day"));
$getTwo = date("n/j/Y", strtotime("+2 day"));
$getThree = date("n/j/Y", strtotime("+3 day"));
$getFour = date("n/j/Y", strtotime("+4 day"));
$getFive = date("n/j/Y", strtotime("+5 day"));
$getSix = date("n/j/Y", strtotime("+6 day"));

$keywords = array($getToday,$getOne,$getTwo,$getThree,$getFour,$getFive,$getSix);

function has_dates($haystack, $wordlist)
{
  $found = false;
  foreach ($wordlist as $w)
  {
    if (stripos($haystack, $w) !== false) {
      $found = true;
      break;
    }
  }
  return $found;
}

$rss = simplexml_load_file("https://www.surgentcpe.com/rss/rss_Surgent.cfm");
foreach ($rss->channel->item as $i)
{
  if (
      has_dates($i->startdate, $keywords)
  )
  {
    $news[] = array
    (
        "theTitle" => $i->title,
        "theDate" => $i->startdate,
        "theTime" => $i->enddate,
        "theCredits" => $i->credits
    );
  }
}

header('Content-Type: text/xml');

$xml = new SimpleXMLElement('<rss version="2.0" encoding="utf-8"></rss>');
$xml->addChild('channel');
$xml->channel->addChild('title', 'Surgent CPE Upcoming Webinars');
foreach($news as $element) {
  // add item element for each article
  $dateParts = explode(" ", $element['theDate']);
  $timeParts = explode(" ", $element['theTime']);
  $dt1  = $dateParts[0];
  $startTime = substr($dateParts[1], 0, -3);
  $startPart = $dateParts[2];
  $endTime = substr($timeParts[1], 0, -3);
  $endPart = $timeParts[2];

  $dt2 = strtotime($dt1);
  $date = date("n/j/Y", $dt2);
  $day = date("j", $dt2);
  $month = date("F", $dt2);


  $titleParts = explode("(", $element["theTitle"]);
  $acrParts = $element["theTitle"];
  $title  = $titleParts[0];
  $acronym = $titleParts[1];
  $ttl = substr($acrParts, 0, -7);
  $acr = substr($acrParts, -5, -1);

  $item = $xml->channel->addChild('item');
  $item->addChild('title', $ttl);
  $item->addChild('acronym', $acr);
  $item->addChild('date', $date);
  $item->addChild('day', $day);
  $item->addChild('month', $month);
  $item->addChild('time', $startTime." ".$startPart." - ".$endTime." ".$endPart);
  $item->addChild('credits', $element['theCredits']);
}

echo $xml->asXML();
?>
