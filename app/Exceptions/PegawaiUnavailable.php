<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PegawaiUnavailable extends HttpException
{
    public function __construct()
    {
        parent::__construct(503, 'Layanan pegawai sementara tidak tersedia. Silakan coba kembali.');
    }
}
