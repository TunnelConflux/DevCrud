<?php
/**
 * Project      : DevCrud
 * File Name    : InputTypes.php
 * Author       : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/07/09 1:18 PM
 */

namespace TunnelConflux\DevCrud\Models\Enums;

final class InputTypes extends Enum
{
    const FILE = "file";
    const TEXT = "text";
    const IMAGE = "image";
    const VIDEO = "video";
    const SELECT = "select";
    const TEXTAREA = "textarea";
    const YES_NO = "YesNo";
}
