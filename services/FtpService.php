<?php

namespace services;

use Exception;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Service for FTP backups.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @package Services
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */
class FtpService
{
    private $host;
    private $port;
    private $username;
    private $password;

    public function __construct()
    {
        $ftp = getSetting('BACKUP_FTP_SERVER');

        $this->port = array_key_exists('port', $ftp) ? $ftp['port'] : 21;
        $this->host = array_key_exists('host', $ftp) ? $ftp['host'] : '';
        $this->username = array_key_exists('username', $ftp) ? $ftp['username'] : '';
        $this->password = array_key_exists('password', $ftp) ? $ftp['password'] : '';
    }

    /**
     * @param string $localFile
     * @param string $remoteFile
     * @return void
     */
    public function upload(string $localFile, string $remoteFile): bool
    {
        if (!$this->host) {
            throw new Exception('FTP host not set in options.');
        }

        $connId = ftp_connect($this->host, $this->port);
        if (!$connId) {
            throw new Exception('Could not connect to host.');
        }

        $connected = ftp_login($connId, $this->username, $this->password);
        if (!$connected) {
            throw new Exception('Could not log into host with supplied credentials.');
        }

        $put = ftp_put($connId, $remoteFile, $localFile, FTP_ASCII);
        if (!$put) {
            throw new Exception('Could not upload file to host.');
        }
            
        ftp_close($connId);

        return true;
    }
}
