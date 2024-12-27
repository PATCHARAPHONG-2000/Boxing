<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../includes/connect.php';

try {
    // Query ข้อมูลจื่อจาก sport_person
    $sqlPerson = "SELECT id, person_red, person_blue FROM sport_person WHERE isActive = 1 LIMIT 1";
    $stmtPerson = $conn->prepare($sqlPerson);
    $stmtPerson->execute();
    $person = $stmtPerson->fetch(PDO::FETCH_ASSOC);

    if ($person) {
        // Query ข้อมูลคะแนนจาก data_score โดยใช้ sport_person_id
        $sqlScore = "SELECT * FROM data_score WHERE sport_person_id = :sport_person_id";
        $stmtScore = $conn->prepare($sqlScore);
        $stmtScore->execute(['sport_person_id' => $person['id']]);
        $score = $stmtScore->fetch(PDO::FETCH_ASSOC);

        // จัดโครงสร้างข้อมูลคะแนน
        $scores = [
            'round1' => [
                'red' => [$score['R1_JR1'] ?? null, $score['R1_JR2'] ?? null, $score['R1_JR3'] ?? null],
                'blue' => [$score['R1_JB1'] ?? null, $score['R1_JB2'] ?? null, $score['R1_JB3'] ?? null]
            ],
            'round2' => [
                'red' => [$score['R2_JR1'] ?? null, $score['R2_JR2'] ?? null, $score['R2_JR3'] ?? null],
                'blue' => [$score['R2_JB1'] ?? null, $score['R2_JB2'] ?? null, $score['R2_JB3'] ?? null]
            ],
            'round3' => [
                'red' => [$score['R3_JR1'] ?? null, $score['R3_JR2'] ?? null, $score['R3_JR3'] ?? null],
                'blue' => [$score['R3_JB1'] ?? null, $score['R3_JB2'] ?? null, $score['R3_JB3'] ?? null]
            ],
            'round4' => [
                'red' => [$score['R4_JR1'] ?? null, $score['R4_JR2'] ?? null, $score['R4_JR3'] ?? null],
                'blue' => [$score['R4_JB1'] ?? null, $score['R4_JB2'] ?? null, $score['R4_JB3'] ?? null]
            ],
            'round5' => [
                'red' => [$score['R5_JR1'] ?? null, $score['R5_JR2'] ?? null, $score['R5_JR3'] ?? null],
                'blue' => [$score['R5_JB1'] ?? null, $score['R5_JB2'] ?? null, $score['R5_JB3'] ?? null]
            ]
        ];

        $response = [
            'status' => 'success',
            'data' => [
                'person_red' => $person['person_red'],
                'person_blue' => $person['person_blue'],
                'scores' => $scores
            ],
            'debug' => [
                'person_id' => $person['id'],
                'has_scores' => !empty($score)
            ]
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'ไม่พบคู่มวยที่กำลังแข่งขัน'
        ];
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage(),
        'debug' => [
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ]
    ];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
?> 