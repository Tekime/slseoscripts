<?php
/**
 * class.kNetTools.php - Kytoo Network Tools
 *
 * A part of Kytoo Web Architecture - http://www.kytoo.com/
 * Copyright (c) 2009 Intavant - http://www.intavant.com/
 * 
 * >>> THIS IS NOT FREE SOFTWARE: DO NOT SELL, SHARE, OR DISSEMINATE ANY PART OF THIS FILE. <<<
 *
 * @copyright   Copyright (c) 2009 Intavant, All Rights Reserved
 * @license     http://www.intavant.com/en/kytoo/license
 * @author      Gabriel Harper - http://www.gabrielharper.com/
 * @version     1.0
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR 
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND 
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * 2009-04-23 [1.0] - Initial release with Kytoo 2.0. 
 *
*/
 
class kNetTools extends kBase
{

    /**
     * @access  private
     * @var     string      $xml_header
    */
    
    var $known_ports = array(
        array('port' => 20, 'name' => 'FTP data (File Transfer Protocol)'),
        array('port' => 21, 'name' => 'FTP (File Transfer Protocol)'),
        array('port' => 22, 'name' => 'SSH (Secure Shell)'),
        array('port' => 23, 'name' => 'Telnet'),
        array('port' => 25, 'name' => 'SMTP (Send Mail Transfer Protocol)'),
        array('port' => 43, 'name' => 'whois'),
        array('port' => 53, 'name' => 'DNS (Domain Name Service)'),
        array('port' => 68, 'name' => 'DHCP (Dynamic Host Control Protocol)'),
        array('port' => 79, 'name' => 'Finger'),
        array('port' => 80, 'name' => 'HTTP (HyperText Transfer Protocol)'),
        array('port' => 110, 'name' => 'POP3 (Post Office Protocol, version 3)'),
        array('port' => 115, 'name' => 'SFTP (Secure File Transfer Protocol)'),
        array('port' => 119, 'name' => 'NNTP (Network New Transfer Protocol)'),
        array('port' => 123, 'name' => 'NTP (Network Time Protocol)'),
        array('port' => 137, 'name' => 'NetBIOS-ns'),
        array('port' => 138, 'name' => 'NetBIOS-dgm'),
        array('port' => 139, 'name' => 'NetBIOS'),
        array('port' => 143, 'name' => 'IMAP (Internet Message Access Protocol)'),
        array('port' => 161, 'name' => 'SNMP (Simple Network Management Protocol)'),
        array('port' => 194, 'name' => 'IRC (Internet Relay Chat)'),
        array('port' => 220, 'name' => 'IMAP3 (Internet Message Access Protocol 3)'),
        array('port' => 389, 'name' => 'LDAP (Lightweight Directory Access Protocol)'),
        array('port' => 443, 'name' => 'SSL (Secure Socket Layer)'),
        array('port' => 445, 'name' => 'SMB (NetBIOS over TCP)'),
        array('port' => 993, 'name' => 'SIMAP (Secure Internet Message Access Protocol)'),
        array('port' => 995, 'name' => 'SPOP (Secure Post Office Protocol)'),
        array('port' => 1243, 'name' => 'SubSeven (Trojan - security risk!)'),
        array('port' => 1352, 'name' => 'Lotus Notes'),
        array('port' => 1433, 'name' => 'Microsoft SQL Server'),
        array('port' => 1494, 'name' => 'Citrix ICA Protocol'),
        array('port' => 1521, 'name' => 'Oracle SQL'),
        array('port' => 1604, 'name' => 'Citrix ICA / Microsoft Terminal Server'),
        array('port' => 2049, 'name' => 'NFS (Network File System)'),
        array('port' => 3306, 'name' => 'mySQL'),
        array('port' => 4000, 'name' => 'ICQ'),
        array('port' => 5010, 'name' => 'Yahoo! Messenger'),
        array('port' => 5190, 'name' => 'AOL Instant Messenger'),
        array('port' => 5632, 'name' => 'PCAnywhere'),
        array('port' => 5800, 'name' => 'VNC'),
        array('port' => 5900, 'name' => 'VNC'),
        array('port' => 6000, 'name' => 'X Windowing System'),
        array('port' => 6699, 'name' => 'Napster'),
        array('port' => 6776, 'name' => 'SubSeven (Trojan - security risk!)'),
        array('port' => 7070, 'name' => 'RealServer / QuickTime'),
        array('port' => 8080, 'name' => 'HTTP'),
        array('port' => 31337, 'name' => 'BackOrifice (Trojan - Security Warning!)')
    );
    
    function kNetTools()
    {
        return true;
    }
    
    function getKnownPorts()
    {
    
        return $this->known_ports;
    }
    
}