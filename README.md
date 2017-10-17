# simple-cdn

## apache
<VirtualHost _default_:443>
        ServerName cdn-icp.pl
        ServerAlias icp-cdn.dev
        SetEnv DEV_MODE true
        DocumentRoot /home/piotrek/workspace/simple-cdn/public
        SSLEngine on
        SSLCertificateFile      /etc/ssl/certs/ssl-cert-snakeoil.pem
        SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key
        <FilesMatch "\.(cgi|shtml|phtml|php)$">
                        SSLOptions +StdEnvVars
        </FilesMatch>
</VirtualHost>
