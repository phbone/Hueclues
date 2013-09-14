<?php

// Bucket Name
if (!class_exists('S3'))
    require_once('S3.php');

//AWS access info
if (!defined('awsAccessKey'))
    define('awsAccessKey', 'AKIAIJ4GROGXAA6WK76A');
if (!defined('awsSecretKey'))
    define('awsSecretKey', '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');

//instantiate the class
$s3 = new S3(awsAccessKey, awsSecretKey);

$bucket = "hueclues.imgs";
//$s3->putBucket($bucket, S3::ACL_PUBLIC_READ);
?>
