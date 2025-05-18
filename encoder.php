<?php

class ARMValueEncoder
{
    private $arch;

    public function __construct($arch = 'ARM32')
    {
        if (!in_array(strtoupper($arch), ['ARM32', 'ARM64'])) {
            throw new InvalidArgumentException("Architecture must be 'ARM32' or 'ARM64'");
        }
        $this->arch = strtoupper($arch);
    }

    public function generateInstructions($type, $singleValue, $startValue, $endValue, $stepValue)
    {
        $results = [];

        if ($type === 'BOOLEAN') {
            $value = ($singleValue ? 1 : 0);
            $results[] = $this->encodeSingleInt($value, true);
        } elseif ($type === 'INT') {
            $results[] = $this->encodeSingleInt((int)$singleValue, false);
        } elseif ($type === 'FLOAT') {
            $results[] = $this->encodeSingleFloat((float)$singleValue);
        } elseif ($type === 'INT_RANGE') {
            for ($i = $startValue; $i <= $endValue; $i += $stepValue) {
                $results[] = $this->encodeSingleInt((int)$i, false);
            }
        } elseif ($type === 'FLOAT_RANGE') {
            for ($f = $startValue; $f <= $endValue; $f += $stepValue) {
                $results[] = $this->encodeSingleFloat((float)$f);
            }
        } else {
            throw new InvalidArgumentException("Unsupported type: $type");
        }

        return $results;
    }

    private function encodeSingleInt($value)
    {
        if ($value < 0 || $value > 65535) {
            throw new InvalidArgumentException("Value out of range.");
        }
    
        if ($this->arch === 'ARM32') {
            // utiliser MOVW pour TOUT
            $movOpcode = 0xE3000000 | ($value & 0xFFF) | (($value & 0xF000) << 4);
            $bxOpcode = 0xE12FFF1E;
    
            $big = $this->formatBigEndian($movOpcode) . ' ' . $this->formatBigEndian($bxOpcode);
            $little = $this->formatLittleEndian($movOpcode) . ' ' . $this->formatLittleEndian($bxOpcode);
    
        } else {
            $movOpcode = 0xD2800000 | (($value & 0xFFFF) << 5);
            $retOpcode = 0xD65F03C0;
    
            $big = $this->formatBigEndian($movOpcode) . ' ' . $this->formatBigEndian($retOpcode);
            $little = $this->formatLittleEndian($movOpcode) . ' ' . $this->formatLittleEndian($retOpcode);
        }
    
        return [
            'big_endian' => $big,
            'little_endian' => $little
        ];
    }
    

    private function encodeSingleFloat($value)
    {
        $packed = ($this->arch === 'ARM32') ? pack('f', $value) : pack('d', $value);
        $little = strtoupper(implode(' ', str_split(bin2hex($packed), 2)));
        $big = strtoupper(implode(' ', array_reverse(str_split(bin2hex($packed), 2))));

        return [
            'big_endian' => $big,
            'little_endian' => $little
        ];
    }

    private function formatBigEndian($opcode)
    {
        $bytes = [];
        for ($i = 0; $i < 4; $i++) {
            $bytes[] = sprintf("%02X", ($opcode >> ($i * 8)) & 0xFF);
        }
        return implode(' ', array_reverse($bytes));
    }

    private function formatLittleEndian($opcode)
    {
        $bytes = [];
        for ($i = 0; $i < 4; $i++) {
            $bytes[] = sprintf("%02X", ($opcode >> ($i * 8)) & 0xFF);
        }
        return implode(' ', $bytes);
    }
}

header('Content-Type: application/json');

$input = $_POST;
$type = strtoupper($input['type'] ?? 'INT');
$singleValue = $input['singleValue'] ?? 0;
$startValue = $input['startValue'] ?? 0;
$endValue = $input['endValue'] ?? 0;
$stepValue = $input['stepValue'] ?? 1;

try {
    $encoder = new ARMValueEncoder($input['architecture'] ?? 'ARM32');
    $results = $encoder->generateInstructions($type, $singleValue, $startValue, $endValue, $stepValue);
    echo json_encode(['results' => $results]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
