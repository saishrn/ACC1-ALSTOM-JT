import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.IOException;
import java.util.LinkedHashSet;
import java.util.ArrayList;
import java.util.List;
import java.util.Set;

public class JTVPendingDates {

    public static void main(String[] args) {

        String host = "ibmjtvsrvfra000";
        String remoteCommand = "cd /home/oracle1 && cat JT_Pending.txt";

        ProcessBuilder pb =
                new ProcessBuilder("ssh", host, remoteCommand);

        pb.redirectErrorStream(true);

        List<String> allLines = new ArrayList<String>();
        Set<String> last20Dates = new LinkedHashSet<String>();

        try {
            Process process = pb.start();

            BufferedReader reader =
                    new BufferedReader(
                            new InputStreamReader(process.getInputStream())
                    );

            String line;
            while ((line = reader.readLine()) != null) {
                line = line.trim();
                if (!line.equals("")) {
                    allLines.add(line);
                }
            }

            process.waitFor();

            // Traverse from bottom (latest) to top (oldest)
            for (int i = allLines.size() - 1; i >= 0; i--) {

                String currentLine = allLines.get(i);
                String[] parts = currentLine.split("\\s+");

                if (parts.length >= 1) {
                    String dateOnly = parts[0];

                    // Defensive: strip time if present
                    if (dateOnly.length() > 11) {
                        dateOnly = dateOnly.substring(0, 11);
                    }

                    if (!last20Dates.contains(dateOnly)) {
                        last20Dates.add(dateOnly);
                    }

                    if (last20Dates.size() == 20) {
                        break;
                    }
                }
            }

            // Print latest → older
            for (String d : last20Dates) {
                System.out.println(d);
            }

        } catch (IOException e) {
            e.printStackTrace();
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
    }
}
