# 🎓 대학교 학생정보서비스

> **BamToll University Of Technical** — Student Information System

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Apache](https://img.shields.io/badge/Apache-2.4-D22128?style=flat&logo=apache&logoColor=white)](https://httpd.apache.org/)

---

## 🌐 바로가기

**[http://192.168.32.129](http://192.168.32.129)**

| 항목 | 내용 |
|---|---|
| 아이디 | `admin` |
| 패스워드 | `admin1234` |

---

## 📋 프로젝트 개요

대구공업대학교 학생정보서비스를 참고하여 제작한  
**밤톨공업대학교(BUT)** 의 학생 성적 및 주소록 관리 웹 시스템입니다.

---

## 🖥️ 시스템 환경

| 구분 | 서버 | IP |
|---|---|---|
| 웹 서버 | web.kcci.edu | 192.168.32.129 |
| DB 서버 | mysql.kcci.edu | 192.168.32.100 |

---

## 🗄️ 데이터베이스 구조

**Database:** `student`

| 테이블 | 컬럼 | 설명 |
|---|---|---|
| `sungjuk` | 번호, 이름, 국어, 영어, 수학 | 학생 성적 |
| `jusorok` | 번호, 이름, 주소 | 주소록 |
| `sungjuk_ranking` | 번호, 이름, 국어, 영어, 수학, 총점, 평균, 순위 | 성적 순위 |

---

## ✨ 주요 기능

### 🔐 로그인
- 세션 기반 인증
- 초록 테마 대각선 레이아웃 디자인

### 📊 대시보드
- 전체 학생 수, 수석, 평균 총점 통계 카드
- 최근 등록 성적 미리보기

### 📝 성적 관리
- 학생 성적 등록 / 수정 / 삭제
- 이름 검색 및 페이지네이션
- 실시간 총점 · 평균 계산

### 🏆 성적 순위
- 시상대 TOP 3 시각화
- 전체 순위표 자동 계산
- 인쇄 기능

### 📖 주소록 관리
- 주소 등록 / 수정 / 삭제
- 이름 · 주소 통합 검색

---

## 📁 파일 구조

```
student-info-system/
├── index.php              # 로그인 페이지
├── logout.php             # 로그아웃
├── main.php               # 대시보드
│
├── sungjuk_list.php       # 성적 목록
├── sungjuk_insert.php     # 성적 등록
├── sungjuk_edit.php       # 성적 수정
├── sungjuk_delete.php     # 성적 삭제
├── sungjuk_ranking.php    # 성적 순위
│
├── jusorok_list.php       # 주소록 목록
├── jusorok_insert.php     # 주소 등록
├── jusorok_edit.php       # 주소 수정
├── jusorok_delete.php     # 주소 삭제
│
├── includes/
│   └── sidebar.php        # 공통 사이드바
│
└── assets/
    └── style.css          # 전체 스타일 (초록 테마)
```

---

## ⚙️ 설치 방법

### 1. 저장소 클론
```bash
git clone https://github.com/bamtoll/student-info-system.git
cd student-info-system
```

### 2. config.php 생성
```php
<?php
define('DB_HOST', 'mysql서버IP');
define('DB_USER', 'root');
define('DB_PASS', '패스워드');
define('DB_NAME', 'student');
define('ADMIN_ID',   'admin');
define('ADMIN_PASS', 'admin1234');
?>
```

### 3. 데이터베이스 초기화
```bash
mysql -u root -p < setup.sql
```

### 4. 웹서버 배포
```bash
sudo cp -r ./* /var/www/html/
sudo systemctl restart httpd
```

---

## 🔒 보안 설정 (Rocky Linux / RHEL)

```bash
# SELinux - PHP → DB 연결 허용
sudo setsebool -P httpd_can_network_connect_db 1

# 방화벽 - HTTP 허용
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload
```

---

## 📸 화면 구성

| 페이지 | 설명 |
|---|---|
| 로그인 | 대각선 사선 레이아웃, BUT 로고 |
| 대시보드 | 통계 카드 4종 |
| 성적 목록 | 검색 + 색상 점수 표시 |
| 성적 순위 | 시상대 + 전체 순위표 |
| 주소록 | 통합 검색 목록 |

---
