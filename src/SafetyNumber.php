<?php

declare(strict_types=1);

namespace Fifthsage\KCT;

/**
 * KCT 안심번호 클래스.
 */
class SafetyNumber
{
    // request
    public const LOGIN_REQUEST = 2500;                  // 서버 로그인 요청.
    public const USE_SAFETY_NUMBER_REQUEST = 2501;      //  사용 등록/수정 요청
    public const RELEASE_SAFETY_NUMBER_REQUEST = 2502;  // 사용 해지 요청
    public const PAUSE_REQUEST = 2503;                  // 번호 사용 일시정지 요청
    public const RELEASE_PAUSE_REQUEST = 2504;          // 번호 사용 일시정지해지 요청
    public const HEALTH_CHECK_REQUEST = 2600;           // 상태체크 요청.

    // response
    public const LOGIN_RESPONSE = 3500;                 // 서버 로그인 응답
    public const USE_SAFETY_NUMBER_RESPONSE = 3501;     // 번호 사용 등록/수정 요청 응답
    public const RELEASE_SAFETY_NUMBER_RESPONSE = 3502; // 번호 사용 해지 요청 응답
    public const PAUSE_RESPONSE = 3503;                 // 번호 사용 일시정지 요청 응답
    public const RELEASE_PAUSE_RESPONSE = 3504;         // 번호 사용 일시정지해지 요청 응답
    public const HEALTH_CHECK_RESPONSE = 3600;          // 상태체크 요청 응답

    // tcp response code
    public const SUCCESS = '00';                        // 성공
    public const PACKET_LENGTH_ERROR = '01';            // 패킷 길이 에러
    public const UNDEFINED_PACKET_NUMBER = '02';        // 정의되지않은 패킷 번호	"2500, 2501, 2502, 2503, 2504, 2600"
    public const UNREGISTERED_COMPANY_ID = '03';        // 사업자코드 에러	미등록 사업자
    public const NOT_ASSIGNED_NUMBER = '04';            // 번호 할당 후 전송 요청	method가 ‘1’이 아닌 경우
    public const PREFIX_ERROR = '05';                   // 050 번호 prefix 맞지않음	050
    public const NUMBER_LENGTH_ERROR = '06';            // 050 번호 길이 맞지 않음	11~12자리
    public const WRONG_PHONE_NUMBER = '07';             // "전화번호 형식이 맞지 않음 (phone1 & phone2)"	국내: 9~11자리
    public const SEPARATE_SYTAX_ERROR = '08';           // 구분자 에러	stx(‘#’), etx(‘$’)
    public const INVALID_USE_FLAG = '09';               // 번호 사용상태 오류 (Invalid Use Flag)	"일시정지요청일 때 use flag가 2,일시정지해지요청일 때 use flag가 1이 아님"
    public const PROTOCOL_IS_NOT_MATCHED = '10';        // 고객사 외부연동방식이 설정값과 다름	소켓방식/FTP방식
    public const DATABASE_ERROR = '11';                 // 등록, 수정, 삭제 오류(DB오류)
    public const UNREGISTERED_NUMBER = '12';            // 지정되지 않은 050 번호	050 번호 미등록
    public const EXCEED_LIMIT = '13';                   // 허용 TPS 초과
    public const UNAUTHORIZED = '14';                   // 등록된 IP정보가 아닌 곳에서 로그인 요청

    private $packet = null;
    private $length = 127;
    private $startSyntax = '#';
    private $endSyntax = '$';

    public function __construct(string $companyId)
    {
        $this->companyId = $companyId;

        $this->initPacket();
    }

    public function login()
    {
        $this->initPacket()
          ->setPacketId(2500);

        return $this;
    }

    public function register(string $safetyNumber, string $targetNumber)
    {
        $this->initPacket()
          ->setPacketId(2501)
          ->setSafetyNumber($safetyNumber)
          ->setTargetNumber($targetNumber);

        return $this;
    }

    public function getPacket()
    {
        return $this->packet;
    }

    public static function getResultCode(string $packet)
    {
        return substr($packet, 26, 2);
    }

    private function initPacket()
    {
        $this->packet = $this->startSyntax.str_pad('', $this->length - 2).$this->endSyntax;

        // method 고정 값 1
        $this->packet = substr_replace($this->packet, 1, 28, 1);
        // use flag 고정 1
        $this->packet = substr_replace($this->packet, 1, 125, 1);
        // company id
        $this->packet = substr_replace($this->packet, str_pad($this->companyId, 8), 5, 8);

        return $this;
    }

    private function setSafetyNumber(string $number)
    {
        $this->packet = substr_replace($this->packet, str_pad(str_replace('-', '', $number), 12), 29, 12);

        return $this;
    }

    private function setTargetNumber(string $number)
    {
        $this->packet = substr_replace($this->packet, str_pad(str_replace('-', '', $number), 12), 41, 12);

        return $this;
    }

    private function setPacketId(int $id)
    {
        $this->packet = substr_replace($this->packet, (string) $id, 1, 4);

        return $this;
    }
}
