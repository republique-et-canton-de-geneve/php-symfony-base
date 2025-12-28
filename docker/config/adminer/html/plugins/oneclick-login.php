<?php

/**
 * Display a list of predefined database servers to login with just one click.
 * Don't use this in production enviroment unless the access is restricted.
 *
 * @see https://www.adminer.org/plugins/#use
 *
 * @author Gio Freitas, https://www.github.com/giofreitas
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class OneClickLogin
{
    public $servers;
    public $driver;

    /**
     * Set supported servers.
     *
     * @param array  $servers
     * @param string $driver
     */
    public function __construct($servers, $driver = 'server')
    {
        $this->servers = $servers;
        $this->driver = $driver;
    }

    public function serverName(?string $server)
    {
        return null;
    }

    public function login($login, $password)
    {
        return true;
    }

    public function connectSsl()
    {
        if ($_GET['pgsql'] ?? false) {
            return $this->servers[$_GET['pgsql']] ?? null;
        }

        return null;
    }

    public function databaseValues($server)
    {
        $databases = $server['databases'];
        if (is_array($databases)) {
            foreach ($databases as $database => $name) {
                if (is_string($database)) {
                    continue;
                }
                unset($databases[$database]);
                if (!isset($databases[$name])) {
                    $databases[$name] = $name;
                }
            }
        }

        return $databases;
    }

    public function loginForm()
    {
        ?>
		</form>
		<table>
			<tr>
				<th><?php echo 'Server'; ?></th>
				<th><?php echo 'User'; ?></th>
				<th><?php echo 'Database'; ?></th>
			</tr>

			<?php
                    foreach ($this->servers as $host => $server) {
                        $databases = isset($server['databases']) ? $server['databases'] : '';
                        if (!is_array($databases)) {
                            $databases = [$databases => $databases];
                        }

                        foreach (array_keys($databases) as $i => $database) {
                            ?>
					<tr>
						<?php if (0 === $i) { ?>
							<td style="vertical-align:middle" rowspan="<?php echo count($databases); ?>"><?php echo isset($server['label']) ? "{$server['label']} ($host)" : $host; ?></td>
							<td style="vertical-align:middle" rowspan="<?php echo count($databases); ?>"><?php echo $server['username']; ?></td>
						<?php } ?>
						<td style="vertical-align:middle"><?php echo $databases[$database]; ?></td>
						<td>
							<form action="" method="post">
								<input type="hidden" name="auth[driver]" value="<?php echo $server['driver']; ?>">
								<input type="hidden" name="auth[server]" value="<?php echo $host; ?>">
								<input type="hidden" name="auth[username]" value="<?php echo htmlentities($server['username']); ?>">
								<input type="hidden" name="auth[password]" value="<?php echo htmlentities($server['pass']); ?>">
								<input type='hidden' name="auth[db]" value="<?php echo htmlentities($database); ?>" />
								<input type='hidden' name="auth[permanent]" value="1" />
								<input type="submit" value="<?php echo 'Enter'; ?>">
							</form>
						</td>
					</tr>
			<?php
                        }
                    }
        ?>
		</table>
		<form action="" method="post">
	<?php
        return true;
    }
}
