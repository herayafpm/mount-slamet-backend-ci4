<?php

function sendEmail($subject, $to, $view)
{
  $email = \Config\Services::email();
  $email->setTo($to);
  $email->setSubject($subject);
  $email->setMessage($view);
  if ($email->send(false)) {
    return true;
  }
  return false;
}
