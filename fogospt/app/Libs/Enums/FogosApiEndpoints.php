<?php
namespace App\Libs\Enums;

class FogosApiEndpoints extends Enum
{
    const NEW_FIRES = '/new/fires';
    const WARNINGS = '/v1/warnings';
    const WARNINGS_MADEIRA = '/v1/madeira/warnings';
    const WEEK_STATS = "/v1/stats/week";
    const GET_FIRE = "/fires?id=";
    const GET_MADEIRA_FIRE = "/madeira/fires?id=";
    const GET_RISK_BY_FIRE = "/fires/danger?id=";
    const GET_STATUS_BY_FIRE = "/fires/status?id=";
    const GET_STATUS_BY_MADEIRA_FIRE = "/madeira/fires/status?id=";
    const STATS_8HOURS = "/v1/stats/8hours";
    const STATS_8HOURS_YESTERDAY = "/v1/stats/8hours/yesterday";
    const STATS_LAST_NIGHT = "/v1/stats/last-night";
    const GET_NOW = "/v1/now";

}