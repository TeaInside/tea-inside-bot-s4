<?php

namespace Isolator;

defined("ISOLATOR_DIR") or die("ISOLATOR_DIR is not defined yet!\n");
defined("ISOLATOR_USER_DIR") or die("ISOLATOR_USER_DIR is not defined yet!\n");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \Isolator
 * @version 4.0
 */
final class Isolator
{
	/**
	 * @var string
	 */
	private $cmd;

	/**
	 * @var int
	 */
	private $boxId = 0;

	/**
	 * @var string
	 */
	private $user = "root";

	/**
	 * @var string
	 */
	private $boxDir = "/var/local/lib/isolate/0/box";

	/**
	 * @var string
	 */
	private $uid;

	/**
	 * @var int
	 */
	private $maxProcesses = 5;

	/**
	 * @var int
	 */
	private $memoryLimit = 524288;

	/**
	 * @var bool
	 */
	public $sharenet = false;

	/**
	 * @var string
	 */
	private $isolateCmd;

	/**
	 * @var string
	 */
	private $homePath;

	/**
	 * @var string
	 */
	private $etcPath;

	/**
	 * @var string
	 */
	private $tmpPath;

	/**
	 * @var string
	 */
	private $stdoutFile;

	/**
	 * @var string
	 */
	private $stderrFile;

	/**
	 * @var string
	 */
	private $absoluteStdoutFile;

	/**
	 * @var string
	 */
	private $absoluteStderrFile;

	/**
	 * @var int
	 */
	private $maxExecutionTime = 30;

	/**
	 * @var int
	 */
	private $maxWallTime = 30;

	/**
	 * @var int
	 */
	private $extraTime = 15;

	/**
	 * @var int
	 */
	private $maxStack = 20480;

	/**
	 * @param string $boxId
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct(int $boxId)
	{
		$this->boxId = $boxId;
		$this->uid = "6".str_repeat("0", 4 - strlen($this->boxId)).$this->boxId;

		$this->buildRoot();

		$this->tmpPath = ISOLATOR_USER_DIR."/u".$this->uid."/tmp";
		$this->homePath = ISOLATOR_USER_DIR."/u".$this->uid."/home";
		$this->etcPath = ISOLATOR_USER_DIR."/u".$this->uid."/etc";

		$this->absoluteStdoutFile = ISOLATOR_USER_DIR."/u".$this->uid."/tmp/.stdout";
		$this->absoluteStderrFile = ISOLATOR_USER_DIR."/u".$this->uid."/tmp/.stderr";

		$this->stdoutFile = "/tmp/.stdout";
		$this->stderrFile = "/tmp/.stderr";

		file_put_contents($this->absoluteStdoutFile, "");
		file_put_contents($this->absoluteStderrFile, "");
		shell_exec("chmod -R 777 ".ISOLATOR_USER_DIR."/u".$this->uid."/tmp");
		shell_exec("chmod -R 000 ".ISOLATOR_USER_DIR."/u".$this->uid."/system-container");

		$scan = scandir("/etc");
		unset($scan[0], $scan[1], $scan[array_search("passwd", $scan)]);
		foreach ($scan as $file) {
			if (! @readlink($f = $this->etcPath."/".$file)) {
				shell_exec("sudo ln -sf /parent_etc/".$file." ".$f);
			}
		}

		if (!file_exists($this->etcPath."/passwd")) {
			file_put_contents($this->etcPath."/passwd", "root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
sys:x:3:3:sys:/dev:/usr/sbin/nologin
sync:x:4:65534:sync:/bin:/bin/sync
games:x:5:60:games:/usr/games:/usr/sbin/nologin
man:x:6:12:man:/var/cache/man:/usr/sbin/nologin
lp:x:7:7:lp:/var/spool/lpd:/usr/sbin/nologin
mail:x:8:8:mail:/var/mail:/usr/sbin/nologin
news:x:9:9:news:/var/spool/news:/usr/sbin/nologin
uucp:x:10:10:uucp:/var/spool/uucp:/usr/sbin/nologin
proxy:x:13:13:proxy:/bin:/usr/sbin/nologin
www-data:x:33:33:www-data:/var/www:/usr/sbin/nologin
backup:x:34:34:backup:/var/backups:/usr/sbin/nologin
list:x:38:38:Mailing List Manager:/var/list:/usr/sbin/nologin
irc:x:39:39:ircd:/var/run/ircd:/usr/sbin/nologin
gnats:x:41:41:Gnats Bug-Reporting System (admin):/var/lib/gnats:/usr/sbin/nologin
systemd-timesync:x:100:102:systemd Time Synchronization,,,:/run/systemd:/bin/false
systemd-network:x:101:103:systemd Network Management,,,:/run/systemd/netif:/bin/false
systemd-resolve:x:102:104:systemd Resolver,,,:/run/systemd/resolve:/bin/false
geoclue:x:103:105::/var/lib/geoclue:/usr/sbin/nologin
syslog:x:104:108::/home/syslog:/bin/false
_apt:x:105:65534::/nonexistent:/bin/false
messagebus:x:106:110::/var/run/dbus:/bin/false
uuidd:x:107:111::/run/uuidd:/bin/false
u{$this->uid}:x:{$this->uid}:{$this->uid}:u{$this->uid},,,:/home/u{$this->uid}");
		}

		if (! is_dir($this->boxDir = $d = "/var/local/lib/isolate/".$this->boxId."/box")) {
			shell_exec("/usr/local/bin/isolate --box-id=".$this->boxId." --init");
			shell_exec("sudo mkdir -p ".$d);
		}
	}

	/**
	 * @param string $str
	 * @return int
	 */
	public static function generateBoxId(string $str): int
	{
		if (file_exists(ISOLATOR_DIR."/box_id_map")) {
			$st = json_decode(file_get_contents(ISOLATOR_DIR."/box_id_map"), true);
			if (isset($st["next_id"], $st["map"]) && is_array($st["map"])) {
				if (isset($st["map"][$str])) {
					return (int)$st["map"][$str];
				} else {
					$st["map"][((string)$str)] = $st["next_id"];
					$st["next_id"]++;
					file_put_contents(ISOLATOR_DIR."/box_id_map", json_encode($st, 128));
					return (int)($st["next_id"] - 1);
				}
			}
		}

		$st["map"][$str] = 0;
		$st["next_id"] = 1;

		file_put_contents(ISOLATOR_DIR."/box_id_map", json_encode($st, 128));
		return 0;
	}

	/**
	 * @return void
	 */
	public function buildRoot()
	{
		$f = ISOLATOR_USER_DIR."/u{$this->uid}";
		foreach(
			[
				$f,
				$f."/home",
				$f."/boot",
				$f."/cdrom",
				$f."/opt",
				$f."/mnt",
				$f."/srv",
				$f."/sbin",
				$f."/var",
				$f."/sys",
				$f."/root",
				$f."/run",
				$f."/tmp",
				$f."/etc",
				$f."/media",
				$f."/lost+found",
				$f."/dockerd",
				$f."/system-container"
			] as $dir
		) {
			is_dir($dir) or mkdir($dir);
		}

		is_dir($f."/home/ubuntu") or shell_exec("sudo cp -rf /etc/skel {$f}/home/ubuntu");
		is_dir($f."/home/u{$this->uid}") or shell_exec("sudo cp -rf /etc/skel {$f}/home/u{$this->uid}");
		is_dir($f."/home/u{$this->uid}/scripts") or mkdir($f."/home/u{$this->uid}/scripts");
		
		shell_exec("chmod -R 755 {$f}/home/u{$this->uid}");
		shell_exec("chown -R {$this->uid}:{$this->uid} {$f}/home/u{$this->uid}");
	}

	/**
	 * @param int $n
	 * @return void
	 */
	public function maxProcesses(int $n): void
	{
		$this->maxProcesses = $n;
	}

	/**
	 * @param int $size
	 * @return void
	 */
	public function memoryLimit(int $size): void
	{
		$this->memoryLimit = $size;
	}

	/**
	 * @param int $n
	 * @return void
	 */
	public function maxWallTime(int $n): void
	{
		$this->maxWallTime = $n;
	}

	/**
	 * @param int $size
	 * @return void
	 */
	public function maxExecutionTime(int $size): void
	{
		$this->maxExecutionTime = $size;
	}

	/**
	 * @param int $size
	 * @return void
	 */
	public function extraTime(int $size): void
	{
		$this->extraTime = $size;
	}

	/**
	 * @param bool $enable
	 * @return void
	 */
	public function sharenet(bool $enable): void
	{
		$this->sharenet = $enable;
	}

	/**
	 * @return string
	 */
	public function getUid(): string
	{
		return $this->uid;
	}

	/**
	 * @param string $cmd
	 * @return void
	 */
	public function run(string $cmd): void
	{
		$this->buildCmd($cmd);
		$this->isolateOut = shell_exec($this->isolateCmd);
	}

	/**
	 * @return string
	 */
	public function getIsolateOut(): string
	{
		return $this->isolateOut;
	}

	/**
	 * @return string
	 */
	public function getResult(): string
	{
		return file_get_contents($this->absoluteStdoutFile);
	}

	/**
	 * @param string $cmd
	 * @return void
	 */
	private function buildCmd(string $cmd): void
	{
		$this->isolateCmd = 
			"/usr/local/bin/isolate ".
			$this->param("dir").
			$this->param("stdout").
			$this->param("stderr").
			$this->param("processes").
			$this->param("memoryLimit").
			$this->param("maxWallTime").
			$this->param("maxExecutionTime").
			$this->param("extraTime").
			$this->param("sharenet").
			$this->param("chdir").
			$this->param("env").
			"--box-id={$this->boxId} --run -- /bin/sh -c ".escapeshellarg($cmd).
			" 2>&1";
	}

	/**
	 * @param string $prm
	 * @return string
	 */
	private function param(string $prm): string
	{
		$param = "";
		switch ($prm) {
			case "dir":
				$param = escapeshellarg("--dir=/home=".$this->homePath.":rw")." ";
				$param.= escapeshellarg("--dir=/tmp=".$this->tmpPath.":rw")." ";
				$param.= escapeshellarg("--dir=/etc=".$this->etcPath.":rw")." ";
				$param.= escapeshellarg("--dir=/parent_etc=/etc:rw")." ";
				$param.= escapeshellarg("--dir=/var=/var:rw")." ";
				$param.= escapeshellarg("--dir=/lib=/lib:rw");
				break;
			case "stdout":
				$param = escapeshellarg("--stdout={$this->stdoutFile}");
				break;
			case "stderr":
				// $param = "--stderr={$this->stderrFile}";
				$param = "--stderr-to-stdout";
				break;
			case "processes":
				$param = "--processes={$this->maxProcesses}";
				break;
			case "memoryLimit":
				$param = "--mem={$this->memoryLimit}";
				break;
			case "maxWallTime":
				$param = "--wall-time={$this->maxWallTime}";
				break;
			case "maxExecutionTime":
				$param = "--time={$this->maxExecutionTime}";
				break;
			case "extraTime":
				$param = "--extra-time={$this->extraTime}";
				break;
			case "sharenet":
				$param = $this->sharenet ? "--share-net" : "";
				break;
			case "chdir":
				$param = "--chdir=/home/u".$this->uid;
				break;
			case "env":
				$param = "--full-env ";
				$param.= "--env=TMPDIR=/tmp";
				break;
			case "maxStack":
				$param = "--stack={$this->maxStack}";
				break;
			default:				
				break;
		}

		return $param === "" ? "" : $param." ";
	}
}
