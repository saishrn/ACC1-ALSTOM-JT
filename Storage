<?php
$cmd = '/usr/bin/ssh '
     . '-i /var/sshkeys_nobody/id_rsa_jtmon '
     . '-o LogLevel=ERROR '
     . '-o StrictHostKeyChecking=no '
     . '-o UserKnownHostsFile=/dev/null '
     . 'oracle1@ibmjtvsrvfra001 '
     . '"/home/oracle1/JTVStorage.sh" 2>&1';
$output = trim(shell_exec($cmd));
$rows = array();
if ($output !== '') {
    foreach (explode("\n", $output) as $line) {
        $cols = explode('|', trim($line));
        if (count($cols) === 7) {
            $rows[] = $cols;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>JT Storage Status</title>
    <style>
        :root {
            --page-bg: #e6f0ff;      /* mild blue background */
            --text: #222;
            --muted: #555;
            --table-border: #c9d4ea; /* subtle bluish border */
            --row-alt: #f0f7ff;      /* light blue zebra row */
            --header-bg: #243447;    /* dark header for contrast */
            --header-text: #ffffff;
            --server-red: #a00000;   /* server column color */
            --used-blue: #1b5e20;    /* % Used column color (updated to green) */
        }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: var(--page-bg);
            margin: 18px;
            color: var(--text);
        }
        /* Top hero image (same style as other pages) */
        .hero {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
            margin-bottom: 16px;
        }
        h2 {
            margin: 6px 0 4px 0;
            font-weight: 700;
        }
        .subtitle {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 16px;
        }
        /* Center the table and keep it "normal" width */
        .table-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        table.storage {
            border-collapse: collapse;
            width: 760px;        /* normal width */
            max-width: 100%;
            background: transparent;
            border: 1px solid var(--table-border);
            border-radius: 8px;
            overflow: hidden;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        th {
            background-color: var(--header-bg);
            color: var(--header-text);
            padding: 10px;
            border: 1px solid #1c2a36;
            text-align: left;
            white-space: nowrap;
        }
        td {
            padding: 9px 10px;
            border: 1px solid var(--table-border);
            text-align: left;
            background-color: #ffffff; /* card-like cells */
        }
        /* Zebra rows in light blue to match theme */
        tbody tr:nth-child(even) td {
            background-color: var(--row-alt);
        }
        /* Server column: bold red, and we'll print uppercase in PHP */
        td.server {
            color: var(--server-red);
            font-weight: bold;
        }
        /* Make GB Blocks, Used, Free bold (columns 4,5,6) */
        td:nth-child(4),
        td:nth-child(5),
        td:nth-child(6) {
            font-weight: bold;
            text-align: right; /* optional: right align numbers */
        }
        /* % Used column (7th) in blue + bold and centered */
        td:nth-child(7) {
            color: var(--used-blue);
            font-weight: bold;
            text-align: center;
        }
        @media (max-width: 820px) {
            table.storage { width: 100%; }
            th, td { padding: 8px; font-size: 13px; }
        }
    </style>
</head>
<body>
    <!-- Image first (same as previous webpages) -->
    <img src="../images/AlstomImage.jpg">
    <h2>JT Storage Status (Refresh this page to see updated values)</h2>
    <div class="subtitle">
        Last refreshed (server time): <?php echo date('Y-m-d H:i:s'); ?>
    </div>
    <div class="table-wrap">
        <table class="storage" aria-label="JT Storage Status">
            <thead>
                <tr>
                    <th>Server</th>
                    <th>Filesystem</th>
                    <th>Mounted On</th>
                    <th>GB Blocks</th>
                    <th>Used</th>
                    <th>Free</th>
                    <th>% Used</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <!-- Capitalize server names: jt1 -> JT1 -->
                    <td class="server"><?php echo strtoupper(htmlspecialchars($r[0])); ?></td>
                    <td><?php echo htmlspecialchars($r[1]); ?></td>
                    <td><?php echo htmlspecialchars($r[6]); ?></td>
                    <td><?php echo htmlspecialchars($r[2]); ?></td>
                    <td><?php echo htmlspecialchars($r[3]); ?></td>
                    <td><?php echo htmlspecialchars($r[4]); ?></td>
                    <td><?php echo htmlspecialchars($r[5]); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
