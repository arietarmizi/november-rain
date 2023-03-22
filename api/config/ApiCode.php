<?php

namespace api\config;

/**
 *
 * XABBCCDD
 * X = 1 for common response, 9 for fatal response
 * A = 1 for success, 0 for error, 2 for success with warning
 * B = 2 digit module code
 * C = 2 feature code
 * D = sequence (00 for success only)
 *
 *
 */
class ApiCode
{
    const DEFAULT_CODE            = 00000;
}