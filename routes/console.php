<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('logs:cleanup')->daily();
