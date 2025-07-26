<?php

abstract class Actions {
    const Zero = 'Transaction successfully completed';
    const One = 'Duplicate transaction detected';
    const Two = 'Transaction declined';
    const Three = 'Transaction processing fault';

    public static function getValueByName($name) {
        $constants = self::getConstants();
        if (array_key_exists($name, $constants)) {
            return $constants[$name];
        }
        return null;
    }

    private static function getConstants() {
        $reflection = new ReflectionClass(__CLASS__);
        return $reflection->getConstants();
    }
}

abstract class ResponseCodes {
    const Dif1 = 'A mandatory request field is not filled in';
    const Dif2 = 'CGI request validation failed';
    const Dif3 = 'Acquirer host (TS) does not respond or wrong format of e-gateway response template file';
    const Dif4 = 'No connection to the acquirer host (TS)';
    const Dif5 = 'The acquirer host (TS) connection failed during transaction processing';
    const Dif6 = 'e-Gateway configuration error';
    const Dif7 = 'The acquirer host (TS) response is invalid, e.g. mandatory fields missing';
    const Dif8 = 'Error in the "Card number" request field';
    const Dif9 = 'Error in the "Card expiration date" request field';
    const Dif10 = 'Error in the "Amount" request field';
    const Dif11 = 'Error in the "Currency" request field';
    const Dif12 = 'Error in the "Merchant ID" request field';
    const Dif13 = 'The referrer IP address (usually the merchant\'s IP) is not the one expected';
    const Dif14 = 'No connection to the internet terminal PIN pad or agent program is not running on the internet
    terminal computer/workstation';
    const Dif15 = 'Error in the "RRN" request field';
    const Dif16 = 'Another transaction is being performed on the terminal';
    const Dif17 = 'The terminal is denied access to e-Gateway';
    const Dif18 = 'Error in the CVC2 or CVC2 Description request fields';
    const Dif19 = 'Error in the authentication information request or authentication failed.';
    const Dif20 = 'The permitted time interval (1 hour by default) between the transaction timestamp request field and
    the e-Gateway time was exceeded';
    const Dif21 = 'The transaction has already been executed';
    const Dif22 = 'Transaction contains invalid authentication information';
    const Dif23 = 'Invalid transaction context';
    const Dif24 = 'Transaction context data mismatch';
    const Dif25 = 'Transaction canceled (e.g. by user)';
    const Dif26 = 'Invalid action BIN';
    const Dif27 = 'Invalid merchant name';
    const Dif28 = 'Invalid incoming addendum(s)';
    const Dif29 = 'Invalid/duplicate authentication reference';
    const Dif30 = 'Transaction was declined as fraud';
    const Dif31 = 'Transaction already in progress';
    const Dif32 = 'Duplicate declined transaction';
    const Dif33 = 'Customer authentication by random amount or verify one-time code in progress';
    const Dif34 = 'Mastercard Installment customer choice in progress';
    const Dif35 = 'Mastercard Installments auto canceled';
    const Dif36 = 'Mastercard Installment user canceled';
    const Dif37 = 'Invalid recurring expiration date';

    public static function getValueByName($name) {
        $constants = self::getConstants();
        if (array_key_exists($name, $constants)) {
            return $constants[$name];
        }
        return null;
    }

    private static function getConstants() {
        $reflection = new ReflectionClass(__CLASS__);
        return $reflection->getConstants();
    }
}

?>