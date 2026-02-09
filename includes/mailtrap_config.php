<?php

class MailtrapSMTP {
    private $host = 'sandbox.smtp.mailtrap.io';
    private $port = 2525;
    private $username = '';
    private $password = '';
    private $socket = null;

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }


    // Connect to Mailtrap SMTP server
    public function connect() {
        $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, 10);
        
        if (!$this->socket) {
            error_log("SMTP Connection failed: $errstr ($errno)");
            return false;
        }

        $response = $this->getResponse();
        if (strpos($response, '220') === false) {
            error_log("SMTP Server greeting error: $response");
            return false;
        }

        return true;
    }

    
    // Send SMTP command and get response
    
    private function sendCommand($cmd) {
        fwrite($this->socket, $cmd . "\r\n");
        return $this->getResponse();
    }


    // Get response from SMTP server
    private function getResponse() {
        $response = '';
        while ($line = fgets($this->socket, 1024)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }

    
    // Authenticate with Mailtrap
    public function authenticate() {
        // Send EHLO
        $this->sendCommand('EHLO localhost');

        // Start TLS
        $response = $this->sendCommand('STARTTLS');
        if (strpos($response, '220') === false) {
            error_log("STARTTLS failed: $response");
            return false;
        }

        // Enable TLS
        stream_context_set_option($this->socket, 'ssl', 'allow_self_signed', true);
        stream_context_set_option($this->socket, 'ssl', 'verify_peer', false);
        stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

        // Send AUTH LOGIN
        $this->sendCommand('AUTH LOGIN');
        
        // Send username (base64 encoded)
        $response = $this->sendCommand(base64_encode($this->username));
        
        // Send password (base64 encoded)
        $response = $this->sendCommand(base64_encode($this->password));
        
        if (strpos($response, '235') === false && strpos($response, '334') === false) {
            error_log("Authentication failed: $response");
            return false;
        }

        return true;
    }

    
    // Send email
    
    public function sendEmail($from, $to, $subject, $body, $isHtml = true) {
        // Mail from
        $response = $this->sendCommand("MAIL FROM:<$from>");
        if (strpos($response, '250') === false) {
            error_log("MAIL FROM failed: $response");
            return false;
        }

        // Recipient
        $response = $this->sendCommand("RCPT TO:<$to>");
        if (strpos($response, '250') === false) {
            error_log("RCPT TO failed: $response");
            return false;
        }

        // Data
        $this->sendCommand('DATA');

        // Construct email headers and body
        $headers = "From: $from\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($isHtml) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        $headers .= "Content-Transfer-Encoding: 8bit\r\n\r\n";

        // Send headers and body
        fwrite($this->socket, $headers);
        fwrite($this->socket, $body);
        fwrite($this->socket, "\r\n.\r\n");

        $response = $this->getResponse();
        if (strpos($response, '250') === false) {
            error_log("Email send failed: $response");
            return false;
        }

        return true;
    }

    
     // Close connection
     
    public function disconnect() {
        if ($this->socket) {
            $this->sendCommand('QUIT');
            fclose($this->socket);
            $this->socket = null;
        }
    }

    public function __destruct() {
        $this->disconnect();
    }
}

/**
 * Send order confirmation email using Mailtrap
 * 
 * @param string $customer_email - Customer email address
 * @param string $customer_name - Customer name
 * @param int $order_id - Order ID
 * @param array $order_data - Order data array
 * @param array $order_items - Array of order items with product details
 * @return bool - True if email sent successfully
 */
function sendOrderConfirmationEmail($customer_email, $customer_name, $order_id, $order_data, $order_items) {
    
    
    $mailtrap_username = '5527666f6c9171';
    $mailtrap_password = 'f7fef20c853033';
    

    // Check if credentials are configured
    if (strpos($mailtrap_username, 'your_') !== false || strpos($mailtrap_password, 'your_') !== false) {
        error_log("Mailtrap credentials not configured. Email not sent.");
        return false;
    }

    try {
        // Calculate total amount from order items
        $total_amount = 0;
        if (!empty($order_items)) {
            foreach ($order_items as $item) {
                $total_amount += ($item['price'] * $item['quantity']);
            }
        }

        // Create SMTP client
        $smtp = new MailtrapSMTP($mailtrap_username, $mailtrap_password);

        // Connect to Mailtrap
        if (!$smtp->connect()) {
            error_log("Failed to connect to Mailtrap");
            return false;
        }

        // Authenticate
        if (!$smtp->authenticate()) {
            error_log("Failed to authenticate with Mailtrap");
            return false;
        }

        // Generate email body
        ob_start();
        include __DIR__ . '/email_template.php';
        $email_body = ob_get_clean();

        // Send email
        $from = 'shop@cms.local';
        $subject = "Order Confirmation #" . str_pad($order_id, 6, '0', STR_PAD_LEFT);
        
        if (!$smtp->sendEmail($from, $customer_email, $subject, $email_body, true)) {
            error_log("Failed to send email to $customer_email");
            return false;
        }

        error_log("Order confirmation email sent successfully to $customer_email for order #$order_id");
        return true;

    } catch (Exception $e) {
        error_log("Email sending error: {$e->getMessage()}");
        return false;
    }
}
?>
