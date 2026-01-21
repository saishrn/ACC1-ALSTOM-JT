<?php
$sshCmd = '/usr/bin/ssh '
        . '-i /var/sshkeys_nobody/id_rsa_jtmon '
        . '-o StrictHostKeyChecking=no '
        . '-o UserKnownHostsFile=/dev/null '
        . '-o LogLevel=ERROR '
        . 'oracle1@ibmplmdusd80 '
        . '"cd /home/oracle1; bash ./JTVProcessed.sh" 2>&1';
$output = shell_exec($sshCmd);
$lines = explode("\n", trim($output));
$rows = array();
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    $parts = explode('|', $line);
    if (count($parts) == 5) {
        $rows[] = $parts;
    }
}
$refreshed = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>JT Processed Status</title>
    <style>
        /* Base */
        :root {
            --page-bg: #e6f0ff;      /* requested page background */
            --text: #222;
            --muted: #555;
            --table-border: #c9d4ea; /* soft bluish border to match page */
            --row-alt: #f0f7ff;      /* light blue zebra row */
            --header-bg: #243447;    /* dark header for contrast */
            --header-text: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: var(--page-bg);
            color: var(--text);
        }
        /* Layout */
        .container {
            width: 95%;
            max-width: 1100px;
            margin: 24px auto;
        }
        /* Top image */
        .hero {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
            margin-bottom: 20px;
        }
        h2 {
            margin: 6px 0 4px 0;
            font-weight: 700;
        }
        .timestamp {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 16px;
        }
        /* Table wrapper to keep table "normal" width and centered */
        .table-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        /* Table itself: narrower, responsive, not full-page */
        table {
            border-collapse: collapse;   /* keep tidy borders */
            width: 760px;                /* requested width */
            max-width: 100%;             /* responsive downscale */
            background: transparent;     /* avoid blocky white slab */
            border: 1px solid var(--table-border);
            border-radius: 8px;
            overflow: hidden;            /* keeps rounded corners on children */
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        th {
            background-color: var(--header-bg);
            color: var(--header-text);
            padding: 10px;
            border: 1px solid #1c2a36;
            text-align: center;
        }
        td {
            padding: 10px;
            border: 1px solid var(--table-border);
            text-align: center;
            background-color: #ffffff; /* subtle card-like cells */
        }
        /* Zebra rows that match the page theme */
        tr:nth-child(even) td {
            background-color: var(--row-alt);
        }
        .db {
            color: #a00000;
            font-weight: bold;
        }
        /* Small screens */
        @media (max-width: 820px) {
            table {
                width: 100%;            /* full container width under 820px */
            }
            th, td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Image first -->
    <img src="../images/AlstomImage.jpg">
    <!-- Heading next -->
    <h2>JT Processed Status (Yesterday)</h2>
    <div class="timestamp">
        Last refreshed (server time): <?php echo htmlspecialchars($refreshed); ?>
    </div>
    <!-- Then the table -->
    <div class="table-wrap">
        <table aria-label="JT Processed Status">
            <tr>
                <th>Database</th>
                <th>Normal</th>
                <th>Extend</th>
                <th>Nolimit</th>
                <th>Total</th>
            </tr>
            <?php foreach ($rows as $row): ?>
            <tr>
                <td class="db"><?php echo htmlspecialchars($row[0]); ?></td>
                <td><?php echo (int)$row[1]; ?></td>
                <td><?php echo (int)$row[2]; ?></td>
                <td><?php echo (int)$row[3]; ?></td>
                <td><strong><?php echo (int)$row[4]; ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>
