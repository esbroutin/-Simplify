<?php

namespace PNORD;

use Exception;

class SimplifyException extends Exception
{
	const RECORD_EXISTS = 101;
	const REFERENCE_DATABASE_NOT_FOUND = 110;
	const SCENARIO_NOT_FOUND = 111;
	const DATABASE_NOT_OPENED = 112;
}