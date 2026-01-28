<?php

$cmd = '/usr/bin/ssh -i /var/sshkeys_nobody/id_rsa_jtmon -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR oracle1@ibmplmdusd80 "cd /home/oracle1; ./JTVPending.sh" 2>&1';
$output = shell_exec($cmd);

// ---------- Parsing logic ----------
function parseRows($raw) {
    $rows = array();
    if (!is_string($raw) || trim($raw) === '') {
        return $rows;
    }
    $lines = preg_split('/\r\n|\r|\n/', $raw);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        // ignore footer/metadata lines
        if (stripos($line, 'exit code') !== false) {
            continue;
        }
        if ($line === '...' || strpos($line, '...') === 0) {
            continue;
        }
        // split on whitespace (collapses multiple spaces/tabs)
        $parts = preg_split('/\s+/', $line);
        if (!is_array($parts) || count($parts) < 2) {
            continue;
        }
        $date = $parts[0];
        $db  = $parts[1];
        // map numeric/text fields
        $normal = isset($parts[2]) ? $parts[2] : '';
        $extend = isset($parts[3]) ? $parts[3] : '';
        $nolimit = isset($parts[4]) ? $parts[4] : '';
        $rows[] = array(
            'date'   => $date,
            'db'     => $db,
            'normal' => $normal,
            'extend' => $extend,
            'nolimit' => $nolimit
        );
    }
    return $rows;
}
$rows = parseRows($output);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8" />
<title>JT Pending Status</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />

<style type="text/css">
    body {
        font-family: Arial, Helvetica, sans-serif;
        margin: 18px;
        color: #222;
        background-color: #e6f0ff;
    }
    h1 {
        margin: 0 0 6px 0;
        font-size: 20px;
    }
    .meta {
        color: #666;
        margin-bottom: 12px;
        font-size: 13px;
    }
    /* Hero image (same style as previous page) */
    .hero {
        display: block;
        width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        margin-bottom: 16px;
    }
    /* Wrapper that centers the table */
    .table-wrap {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-bottom: 24px;
    }
    table.jt {
        border-collapse: collapse;
        width: 760px;       /* “normal” width */
        max-width: 100%;    /* responsive downscale */
        background: transparent;
        border: 1px solid #c9d4ea;   /* subtle bluish border to match bg */
        border-radius: 8px;
        overflow: hidden;            /* keep rounded corners */
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    table.jt th, table.jt td {
        border: 1px solid #c9d4ea;
        padding: 8px 10px;
        text-align: left;
        font-size: 14px;
    }
    /* Header styling */
    table.jt thead th {
        background: #243447; /* dark header for contrast */
        color: #fff;
        font-weight: bold;
        text-align: center;
    }
    /* Alternate row shading */
    table.jt tbody tr:nth-child(even) td {
        background: #f0f7ff; /* light blue zebra to match theme */
    }
    /* Cells default background (odd rows) */
    table.jt tbody tr:nth-child(odd) td {
        background: #ffffff;
    }
    /* Database column values = Bold Red */
    table.jt td:nth-child(2) {
        color: #a00000; /* deep red */
        font-weight: bold;
        text-align: center;
    }
    /* Numeric columns = Bold, centered */
    table.jt td:nth-child(3),
    table.jt td:nth-child(4),
    table.jt td:nth-child(5) {
        color: #000;
        font-weight: bold;
        text-align: center;
    }
    .notice {
        color: #b33;
        font-weight: 600;
        margin-top: 8px;
    }
    pre.raw {
        margin-top: 12px;
        padding: 10px;
        border: 1px solid #ccc;
        font-family: "Courier New", monospace;
        white-space: pre-wrap;
        background: #fff;
        border-radius: 6px;
    }
    @media (max-width: 820px) {
        table.jt { width: 100%; }
        table.jt th, table.jt td { padding: 8px; font-size: 14px; }
    }
</style>

</head>

<body>

    <img src="../images/AlstomImage.jpg">
    <h1>JT Pending Status (Refresh this page to see updated values)</h1>
    <div class="meta">Last refreshed (server time): <?php echo date('Y-m-d H:i:s'); ?></div>

<?php if ($output === null || trim($output) === ''): ?>
    <div class="notice">
        No output received from JTVPending.sh - Please verify SSH connectivity and that the remote script runs correctly.
    </div>

<?php else: ?>
    <?php if (count($rows) === 0): ?>
        <div class="notice">Output returned but no parseable data lines were found — showing raw output below.</div>
        <pre class="raw"><?php echo htmlspecialchars($output, ENT_QUOTES, 'UTF-8'); ?></pre>
    <?php else: ?>

        <!-- Centered table -->
        <div class="table-wrap">
            <table class="jt" aria-label="JT Pending status table">
                
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Database</th>
                        <th>Normal</th>
                        <th>Extend</th>
                        <th>Nolimit</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($r['db'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($r['normal'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($r['extend'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($r['nolimit'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    
    <?php endif; ?>
<?php endif; ?>

</body>
</html>

========================================================================================================

<?php

$cmd = '/usr/bin/ssh -i /var/sshkeys_nobody/id_rsa_jtmon -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR oracle1@ibmplmdusd80 "cd /home/oracle1; ./JTVPending.sh" 2>&1';
$output = shell_exec($cmd);

// ---------- Parsing logic ----------
function parseRows($raw) {
    $rows = array();

    if (!is_string($raw) || trim($raw) === '') {
        return $rows;
    }

    $lines = preg_split('/\r\n|\r|\n/', $raw);

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;

        // Ignore footer / noise lines
        if (stripos($line, 'exit code') !== false) continue;
        if ($line === '...' || strpos($line, '...') === 0) continue;

        $parts = preg_split('/\s+/', $line);
        if (count($parts) < 2) continue;

        $date = $parts[0];
        $db   = $parts[1];

        $normal  = isset($parts[2]) ? (int)$parts[2] : 0;
        $extend  = isset($parts[3]) ? (int)$parts[3] : 0;
        $nolimit = isset($parts[4]) ? (int)$parts[4] : 0;

        $total = $normal + $extend + $nolimit;

        $rows[] = array(
            'date'    => $date,
            'db'      => $db,
            'normal'  => $normal,
            'extend'  => $extend,
            'nolimit' => $nolimit,
            'total'   => $total
        );
    }

    return $rows;
}

$rows = parseRows($output);

// ---------- Sort by TOTAL (descending) ----------
function sortByTotalDesc($a, $b) {
    if ($a['total'] == $b['total']) {
        return 0;
    }
    return ($a['total'] < $b['total']) ? 1 : -1;
}
usort($rows, 'sortByTotalDesc');

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>JT Pending Status</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        margin: 18px;
        background-color: #e6f0ff;
        color: #222;
    }
    h1 {
        font-size: 20px;
        margin-bottom: 6px;
    }
    .meta {
        color: #666;
        font-size: 13px;
        margin-bottom: 12px;
    }
    .table-wrap {
        display: flex;
        justify-content: center;
    }
    table.jt {
        width: 760px;
        max-width: 100%;
        border-collapse: collapse;
        border: 1px solid #c9d4ea;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    table.jt th, table.jt td {
        padding: 8px 10px;
        border: 1px solid #c9d4ea;
        font-size: 14px;
        text-align: center;
    }
    table.jt thead th {
        background: #243447;
        color: #fff;
        font-weight: bold;
    }
    table.jt tbody tr:nth-child(even) td {
        background: #f0f7ff;
    }
    table.jt tbody tr:nth-child(odd) td {
        background: #ffffff;
    }
    table.jt td:nth-child(2) {
        color: #a00000;
        font-weight: bold;
    }
    table.jt td:nth-child(6) {
        color: #003366;
        font-weight: bold;
    }
    .notice {
        color: #b33;
        font-weight: 600;
        margin-top: 10px;
    }
</style>
</head>

<body>

<img src="../images/AlstomImage.jpg" alt="Header Image">

<h1>JT Pending Status (Refresh this page to see updated values)</h1>
<div class="meta">Last refreshed (server time): <?php echo date('Y-m-d H:i:s'); ?></div>

<?php if (!$output || trim($output) === ''): ?>

    <div class="notice">
        No output received from JTVPending.sh. Please verify SSH connectivity.
    </div>

<?php elseif (count($rows) === 0): ?>

    <div class="notice">Output received but no valid data rows found.</div>
    <pre><?php echo htmlspecialchars($output, ENT_QUOTES, 'UTF-8'); ?></pre>

<?php else: ?>

<div class="table-wrap">
<table class="jt" aria-label="JT Pending status table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Database</th>
            <th>Normal</th>
            <th>Extend</th>
            <th>Nolimit</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?php echo htmlspecialchars($r['date'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['db'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo $r['normal']; ?></td>
            <td><?php echo $r['extend']; ?></td>
            <td><?php echo $r['nolimit']; ?></td>
            <td><?php echo $r['total']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php endif; ?>

</body>
</html>
