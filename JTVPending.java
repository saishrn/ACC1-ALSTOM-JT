import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

public class JTVPending {
	public static void main(String[] args) {
		
		String host = "ibmjtvsrvfra000";
		String remoteCommand = "./jt_pending.ksh; tail -15 JT_Pending.txt";
		
		ProcessBuilder pb = new ProcessBuilder("ssh", host, remoteCommand);
		pb.redirectErrorStream(true);
		
		try {
			Process process = pb.start();
			BufferedReader reader = new BufferedReader(
				new InputStreamReader(process.getInputStream())
			);
			String line;
			while ((line = reader.readLine()) != null) {
				System.out.println(line);
			}
			int exitCode = process.waitFor();
			System.out.println("Exit code: " + exitCode);
		} catch (IOException | InterruptedException e) {
			System.out.println("Error while running remote command: " + e.getMessage());
			e.printStackTrace();
		}
	}
}
