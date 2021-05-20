<?php

$fails = [];
$warnings = [];
$notices = [];

$github = json_decode($_SERVER['GITHUB_CONTEXT'], true);

$ch = curl_init($github['event']['pull_request']['_links']['commits']['href']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'authorization: Bearer ' . $_SERVER['GITHUB_TOKEN'],
    'user-agent: FroshyBot'
]);
$commitsBody = curl_exec($ch);
curl_close($ch);

$commits = json_decode($commitsBody, true);

function render(string $name, string $emoji, array $entries): string
{
    if (\count($entries) === 0) {
        return '';
    }

    $tableTpl = <<<TABLE
<table>
  <thead>
    <tr>
      <th width="50"></th>
      <th width="100%">##NAME##</th>
    </tr>
  </thead>
  <tbody>
    ##CONTENT##
  </tbody>
</table>
TABLE;

    $itemTpl = <<<ITEM
<tr>
      <td>##EMOJI##</td>
      <td>##MSG##</td>
    </tr>
ITEM;


    $items = '';

    foreach ($entries as $entry) {
        $items .= \str_replace(['##EMOJI##', '##MSG##'], [$emoji, $entry], $itemTpl);
    }

    return \str_replace(['##NAME##', '##CONTENT##'], [$name, $items], $tableTpl);
}

require __DIR__ . '/checks.php';

$content = render('Fails', ':no_entry_sign:', $fails) . render('Warnings', ':warning:', $warnings) . render('Notice', ':book:', $notices);

echo '::set-output name=BODY::'. str_replace(['%', "\n", '\r'], ['%25', '%0A', '%0D'], empty($content) ? 'clear' : $content);

