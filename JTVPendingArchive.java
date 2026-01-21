import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.IOException;
import java.util.LinkedHashMap;
import java.util.Map;

public class JTVPendingArchive {
    public static void main(String[] args) {

        if (args.length != 1) {
            System.out.println("Usage: java JTVPendingArchive <DD-Mon-YYYY>");
            System.exit(1);
        }

        String date = args[0];
        String host = "ibmjtvsrvfra000";
        String remoteCommand = "cd /home/oracle1 && cat JT_Pending.txt";

        ProcessBuilder pb = new ProcessBuilder("ssh", host, remoteCommand);
        pb.redirectErrorStream(true);

        // Key = DB name, Value = formatted output line
        Map<String, String> latestByDb = new LinkedHashMap<String, String>();

        try {
            Process process = pb.start();
            BufferedReader reader = new BufferedReader(
                new InputStreamReader(process.getInputStream())
            );

            String line;
            while ((line = reader.readLine()) != null) {
                line = line.trim();
                if (line.isEmpty()) {
                    continue;
                }
                // Match lines starting with the requested date
                if (line.startsWith(date)) {
                    String[] parts = line.split("\\s+");
                    // Expected format: DATE DB VAL1 VAL2 VAL3
                    if (parts.length >= 5) {
                        String db = parts[1];
                        // Format output manually
                        String formatted =
                                parts[0] + "|" +
                                parts[1] + "|" +
                                parts[2] + "|" +
                                parts[3] + "|" +
                                parts[4];
                        // Overwrite ensures latest entry per DB
                        latestByDb.put(db, formatted);
                    }
                }
            }

            process.waitFor();
            // Output final latest values per DB
            for (String value : latestByDb.values()) {
                System.out.println(value);
            }
        } catch (IOException e) {
            System.out.println("IO error: " + e.getMessage());
            e.printStackTrace();
        } catch (InterruptedException e) {
            System.out.println("Execution interrupted: " + e.getMessage());
            e.printStackTrace();
        }
    }
}
