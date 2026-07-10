-- ============================================================
-- Padel Booking Platform — PostgreSQL Schema (Laravel-ready)
-- Multi-tenant, multi-club, all features per PRD
-- ============================================================

-- ---------------------------
-- EXTENSIONS
-- ---------------------------
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ---------------------------
-- TENANTS / CLUBS
-- ---------------------------
CREATE TABLE clubs (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'ID',
    timezone VARCHAR(100) NOT NULL DEFAULT 'Asia/Jakarta',
    phone VARCHAR(50),
    email VARCHAR(255),
    website VARCHAR(255),
    logo_path VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    opening_time TIME NOT NULL DEFAULT '06:00',
    closing_time TIME NOT NULL DEFAULT '23:00',
    advance_booking_days INT DEFAULT 14,
    cancellation_hours INT DEFAULT 24,
    auto_confirm_booking BOOLEAN DEFAULT FALSE,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_clubs_slug ON clubs(slug);
CREATE INDEX idx_clubs_city ON clubs(city);
CREATE INDEX idx_clubs_active ON clubs(is_active);

-- ---------------------------
-- USERS (Global across platform)
-- ---------------------------
CREATE TYPE user_role AS ENUM ('player', 'coach', 'club_admin', 'staff', 'super_admin');
CREATE TYPE user_status AS ENUM ('active', 'suspended', 'pending_verification');

CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(50) UNIQUE,
    password_hash VARCHAR(255),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    full_name VARCHAR(201) GENERATED ALWAYS AS (first_name || ' ' || last_name) STORED,
    avatar_path VARCHAR(500),
    date_of_birth DATE,
    gender VARCHAR(20),
    skill_level INT DEFAULT 1 CHECK (skill_level BETWEEN 1 AND 7), -- 1 beginner, 7 pro
    preferred_hand VARCHAR(20), -- 'left', 'right', 'ambidextrous'
    bio TEXT,
    status user_status DEFAULT 'pending_verification',
    email_verified_at TIMESTAMP NULL,
    phone_verified_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45),
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_phone ON users(phone);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_users_skill ON users(skill_level);

-- ---------------------------
-- SOCIAL AUTH (Google, Apple, etc)
-- ---------------------------
CREATE TABLE user_social_accounts (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    provider VARCHAR(50) NOT NULL, -- 'google', 'apple', 'facebook'
    provider_user_id VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    avatar_url VARCHAR(500),
    token JSONB,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(provider, provider_user_id)
);

CREATE INDEX idx_user_social_user ON user_social_accounts(user_id);

-- ---------------------------
-- CLUB MEMBERS (Many-to-Many: user <-> club)
-- ---------------------------
CREATE TYPE member_role AS ENUM ('member', 'coach', 'staff', 'admin');
CREATE TYPE member_status AS ENUM ('active', 'invited', 'suspended');

CREATE TABLE club_members (
    id BIGSERIAL PRIMARY KEY,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role member_role DEFAULT 'member',
    status member_status DEFAULT 'active',
    joined_at TIMESTAMP DEFAULT NOW(),
    left_at TIMESTAMP NULL,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(club_id, user_id)
);

CREATE INDEX idx_club_members_club ON club_members(club_id);
CREATE INDEX idx_club_members_user ON club_members(user_id);
CREATE INDEX idx_club_members_role ON club_members(role);

-- ---------------------------
-- COURTS
-- ---------------------------
CREATE TYPE court_surface AS ENUM ('artificial_grass', 'natural_grass', 'concrete', 'synthetic');
CREATE TYPE court_type AS ENUM ('indoor', 'outdoor', 'covered');

CREATE TABLE courts (
    id BIGSERIAL PRIMARY KEY,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    court_type court_type NOT NULL DEFAULT 'outdoor',
    surface_type court_surface NOT NULL DEFAULT 'artificial_grass',
    is_available BOOLEAN DEFAULT TRUE,
    is_premium BOOLEAN DEFAULT FALSE,
    base_price DECIMAL(10,2) NOT NULL,
    peak_price DECIMAL(10,2),
    off_peak_price DECIMAL(10,2),
    booking_slot_duration INT DEFAULT 60, -- minutes
    max_players INT DEFAULT 4,
    description TEXT,
    image_path VARCHAR(500),
    order_index INT DEFAULT 0,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(club_id, slug)
);

CREATE INDEX idx_courts_club ON courts(club_id);
CREATE INDEX idx_courts_available ON courts(is_available);

-- ---------------------------
-- COURT AVAILABILITY / SCHEDULE OVERRIDES
-- ---------------------------
CREATE TYPE availability_exception_type AS ENUM ('blocked', 'special_hours', 'maintenance');

CREATE TABLE court_availability (
    id BIGSERIAL PRIMARY KEY,
    court_id BIGINT NOT NULL REFERENCES courts(id) ON DELETE CASCADE,
    exception_type availability_exception_type DEFAULT 'blocked',
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    reason VARCHAR(255),
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_court_availability_court_time ON court_availability(court_id, start_time, end_time);

-- ---------------------------
-- PRICING RULES (peak/off-peak, day-based)
-- ---------------------------
CREATE TABLE court_pricing (
    id BIGSERIAL PRIMARY KEY,
    court_id BIGINT NOT NULL REFERENCES courts(id) ON DELETE CASCADE,
    day_of_week INT CHECK (day_of_week BETWEEN 0 AND 6), -- 0=Sunday
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_court_pricing_court ON court_pricing(court_id);

-- ---------------------------
-- COACHES (extends user within club)
-- ---------------------------
CREATE TABLE coaches (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    hourly_rate DECIMAL(10,2) NOT NULL,
    rating_avg DECIMAL(3,2) DEFAULT 0.00,
    rating_count INT DEFAULT 0,
    bio TEXT,
    certifications JSONB DEFAULT '[]'::jsonb,
    specialties JSONB DEFAULT '[]'::jsonb,
    is_active BOOLEAN DEFAULT TRUE,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, club_id)
);

CREATE INDEX idx_coaches_club ON coaches(club_id);
CREATE INDEX idx_coaches_user ON coaches(user_id);

-- ---------------------------
-- COACH AVAILABILITY (recurring or one-off)
-- ---------------------------
CREATE TABLE coach_availability (
    id BIGSERIAL PRIMARY KEY,
    coach_id BIGINT NOT NULL REFERENCES coaches(id) ON DELETE CASCADE,
    day_of_week INT CHECK (day_of_week BETWEEN 0 AND 6),
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_recurring BOOLEAN DEFAULT TRUE,
    specific_date DATE,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_coach_availability_coach ON coach_availability(coach_id);

-- ---------------------------
-- MEMBERSHIP PLANS
-- ---------------------------
CREATE TABLE membership_plans (
    id BIGSERIAL PRIMARY KEY,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'IDR',
    duration_days INT NOT NULL, -- 30, 90, 365
    booking_credits INT DEFAULT 0,
    discount_percentage DECIMAL(5,2) DEFAULT 0.00,
    features JSONB DEFAULT '[]'::jsonb,
    max_bookings_per_week INT,
    priority_booking_hours INT DEFAULT 0, -- can book X hours before others
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(club_id, slug)
);

CREATE INDEX idx_membership_plans_club ON membership_plans(club_id);

-- ---------------------------
-- USER MEMBERSHIPS
-- ---------------------------
CREATE TYPE membership_status AS ENUM ('active', 'expired', 'cancelled', 'pending');

CREATE TABLE user_memberships (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    plan_id BIGINT NOT NULL REFERENCES membership_plans(id),
    status membership_status DEFAULT 'pending',
    starts_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    auto_renew BOOLEAN DEFAULT FALSE,
    credits_remaining INT DEFAULT 0,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_user_memberships_user ON user_memberships(user_id);
CREATE INDEX idx_user_memberships_club ON user_memberships(club_id);
CREATE INDEX idx_user_memberships_status ON user_memberships(status);

-- ---------------------------
-- CREDITS / WALLET
-- ---------------------------
CREATE TYPE credit_type AS ENUM ('purchase', 'bonus', 'refund', 'deduction');
CREATE TYPE credit_source AS ENUM ('payment', 'membership', 'promo', 'manual');

CREATE TABLE user_credits (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    amount DECIMAL(12,2) NOT NULL,
    type credit_type NOT NULL,
    source credit_source NOT NULL,
    balance_after DECIMAL(12,2) NOT NULL,
    reference_id BIGINT, -- booking_id or membership_id
    reference_type VARCHAR(50),
    notes TEXT,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_user_credits_user_club ON user_credits(user_id, club_id);

-- ---------------------------
-- BOOKINGS
-- ---------------------------
CREATE TYPE booking_status AS ENUM (
    'pending', 'confirmed', 'cancelled', 'completed', 'no_show', 'in_progress'
);
CREATE TYPE booking_type AS ENUM (
    'regular', 'recurring', 'lesson', 'tournament', 'walk_in', 'waitlist'
);
CREATE TYPE payment_status AS ENUM (
    'pending', 'paid', 'partially_paid', 'failed', 'refunded', 'voided'
);

CREATE TABLE bookings (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    court_id BIGINT NOT NULL REFERENCES courts(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE, -- primary booker
    coach_id BIGINT REFERENCES coaches(id) ON DELETE SET NULL,
    booking_type booking_type DEFAULT 'regular',
    status booking_status DEFAULT 'pending',
    payment_status payment_status DEFAULT 'pending',
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    duration_minutes INT GENERATED ALWAYS AS (
        EXTRACT(EPOCH FROM (end_time - start_time)) / 60
    ) STORED,
    number_of_players INT DEFAULT 4,
    players JSONB DEFAULT '[]'::jsonb, -- [{user_id, name, paid, status}]
    is_recurring BOOLEAN DEFAULT FALSE,
    recurring_group_uuid UUID,
    recurring_rule JSONB, -- iCal RRULE format
    total_amount DECIMAL(12,2) NOT NULL,
    discount_amount DECIMAL(12,2) DEFAULT 0.00,
    tax_amount DECIMAL(12,2) DEFAULT 0.00,
    final_amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'IDR',
    notes TEXT,
    source VARCHAR(50) DEFAULT 'app', -- 'app', 'walk_in', 'phone', 'web'
    qr_code_path VARCHAR(500),
    checked_in_at TIMESTAMP NULL,
    checked_out_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    cancelled_by BIGINT REFERENCES users(id),
    cancelled_at TIMESTAMP NULL,
    refund_amount DECIMAL(12,2) DEFAULT 0.00,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_bookings_club_court_time ON bookings(club_id, court_id, start_time, end_time);
CREATE INDEX idx_bookings_user ON bookings(user_id);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_recurring ON bookings(recurring_group_uuid) WHERE is_recurring = TRUE;
CREATE INDEX idx_bookings_start ON bookings(start_time);

-- ---------------------------
-- BOOKING PARTICIPANTS (for split payment / matchmaking)
-- ---------------------------
CREATE TABLE booking_participants (
    id BIGSERIAL PRIMARY KEY,
    booking_id BIGINT NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    guest_name VARCHAR(255),
    guest_email VARCHAR(255),
    guest_phone VARCHAR(50),
    is_organizer BOOLEAN DEFAULT FALSE,
    payment_status payment_status DEFAULT 'pending',
    share_amount DECIMAL(10,2) DEFAULT 0.00,
    joined_at TIMESTAMP DEFAULT NOW(),
    metadata JSONB DEFAULT '{}'::jsonb
);

CREATE INDEX idx_booking_participants_booking ON booking_participants(booking_id);
CREATE INDEX idx_booking_participants_user ON booking_participants(user_id);

-- ---------------------------
-- WAITLIST
-- ---------------------------
CREATE TABLE waitlist_entries (
    id BIGSERIAL PRIMARY KEY,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    court_id BIGINT NOT NULL REFERENCES courts(id) ON DELETE CASCADE,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    guest_name VARCHAR(255),
    guest_email VARCHAR(255),
    guest_phone VARCHAR(50),
    desired_start_time TIMESTAMP NOT NULL,
    desired_end_time TIMESTAMP NOT NULL,
    duration_minutes INT NOT NULL,
    number_of_players INT DEFAULT 4,
    status VARCHAR(50) DEFAULT 'waiting', -- waiting, notified, converted, expired, cancelled
    notified_at TIMESTAMP NULL,
    converted_booking_id BIGINT REFERENCES bookings(id),
    expires_at TIMESTAMP NOT NULL,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_waitlist_court_time ON waitlist_entries(court_id, desired_start_time);
CREATE INDEX idx_waitlist_status ON waitlist_entries(status);

-- ---------------------------
-- PAYMENTS
-- ---------------------------
CREATE TYPE payment_method AS ENUM ('credit_card', 'debit_card', 'e_wallet', 'bank_transfer', 'qris', 'cash', 'credit');
CREATE TYPE payment_gateway AS ENUM ('midtrans', 'xendit', 'manual', 'stripe');

CREATE TABLE payments (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    booking_id BIGINT NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    participant_id BIGINT REFERENCES booking_participants(id) ON DELETE CASCADE,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'IDR',
    method payment_method NOT NULL,
    gateway payment_gateway NOT NULL,
    gateway_transaction_id VARCHAR(255),
    gateway_response JSONB,
    status VARCHAR(50) DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    refunded_at TIMESTAMP NULL,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_payments_booking ON payments(booking_id);
CREATE INDEX idx_payments_user ON payments(user_id);
CREATE INDEX idx_payments_gateway_txn ON payments(gateway_transaction_id);

-- ---------------------------
-- MEMBERSHIP TRANSACTIONS
-- ---------------------------
CREATE TABLE membership_transactions (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    user_membership_id BIGINT NOT NULL REFERENCES user_memberships(id) ON DELETE CASCADE,
    payment_id BIGINT REFERENCES payments(id),
    type VARCHAR(50) NOT NULL, -- 'purchase', 'renewal', 'upgrade', 'cancellation'
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'IDR',
    status VARCHAR(50) DEFAULT 'pending',
    gateway_transaction_id VARCHAR(255),
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- ---------------------------
-- EQUIPMENT / RENTAL ITEMS
-- ---------------------------
CREATE TABLE equipment (
    id BIGSERIAL PRIMARY KEY,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100), -- 'racket', 'ball', 'shoes', 'accessories'
    sku VARCHAR(100),
    price_per_hour DECIMAL(10,2) NOT NULL,
    price_per_day DECIMAL(10,2),
    security_deposit DECIMAL(10,2) DEFAULT 0.00,
    quantity_total INT NOT NULL,
    quantity_available INT NOT NULL,
    condition_status VARCHAR(50) DEFAULT 'good', -- 'good', 'fair', 'damaged', 'retired'
    image_path VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_equipment_club ON equipment(club_id);
CREATE INDEX idx_equipment_active ON equipment(is_active);

-- ---------------------------
-- EQUIPMENT RENTALS
-- ---------------------------
CREATE TYPE rental_status AS ENUM ('reserved', 'picked_up', 'returned', 'cancelled', 'lost');

CREATE TABLE equipment_rentals (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    booking_id BIGINT NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    equipment_id BIGINT NOT NULL REFERENCES equipment(id) ON DELETE CASCADE,
    status rental_status DEFAULT 'reserved',
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    security_deposit DECIMAL(10,2) DEFAULT 0.00,
    deposit_returned BOOLEAN DEFAULT FALSE,
    pickup_at TIMESTAMP NULL,
    return_at TIMESTAMP NULL,
    due_at TIMESTAMP NOT NULL,
    damage_report TEXT,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_equipment_rentals_booking ON equipment_rentals(booking_id);
CREATE INDEX idx_equipment_rentals_user ON equipment_rentals(user_id);

-- ---------------------------
-- MATCHMAKING / GAMES
-- ---------------------------
CREATE TYPE match_status AS ENUM ('open', 'full', 'in_progress', 'completed', 'cancelled');

CREATE TABLE matches (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    court_id BIGINT NOT NULL REFERENCES courts(id) ON DELETE CASCADE,
    created_by BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    status match_status DEFAULT 'open',
    skill_level_min INT CHECK (skill_level_min BETWEEN 1 AND 7),
    skill_level_max INT CHECK (skill_level_max BETWEEN 1 AND 7),
    max_players INT DEFAULT 4,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    is_competitive BOOLEAN DEFAULT FALSE,
    description TEXT,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_matches_club_court ON matches(club_id, court_id, start_time);
CREATE INDEX idx_matches_status ON matches(status);

-- ---------------------------
-- MATCH PARTICIPANTS
-- ---------------------------
CREATE TABLE match_participants (
    id BIGSERIAL PRIMARY KEY,
    match_id BIGINT NOT NULL REFERENCES matches(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    skill_level INT CHECK (skill_level BETWEEN 1 AND 7),
    is_confirmed BOOLEAN DEFAULT FALSE,
    joined_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(match_id, user_id)
);

CREATE INDEX idx_match_participants_match ON match_participants(match_id);

-- ---------------------------
-- TOURNAMENTS / LEAGUES
-- ---------------------------
CREATE TYPE tournament_format AS ENUM ('single_elimination', 'double_elimination', 'round_robin', 'swiss');
CREATE TYPE tournament_status AS ENUM ('draft', 'open', 'in_progress', 'completed', 'cancelled');

CREATE TABLE tournaments (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    format tournament_format NOT NULL,
    status tournament_status DEFAULT 'draft',
    max_participants INT,
    min_skill_level INT CHECK (min_skill_level BETWEEN 1 AND 7),
    max_skill_level INT CHECK (max_skill_level BETWEEN 1 AND 7),
    registration_start TIMESTAMP NOT NULL,
    registration_end TIMESTAMP NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    entry_fee DECIMAL(10,2) DEFAULT 0.00,
    prize_description TEXT,
    rules JSONB DEFAULT '[]'::jsonb,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(club_id, slug)
);

CREATE INDEX idx_tournaments_club ON tournaments(club_id);
CREATE INDEX idx_tournaments_status ON tournaments(status);

-- ---------------------------
-- TOURNAMENT BRACKETS / MATCHES
-- ---------------------------
CREATE TYPE tournament_match_status AS ENUM ('scheduled', 'in_progress', 'completed', 'walkover');

CREATE TABLE tournament_matches (
    id BIGSERIAL PRIMARY KEY,
    tournament_id BIGINT NOT NULL REFERENCES tournaments(id) ON DELETE CASCADE,
    round INT NOT NULL,
    match_number INT NOT NULL,
    bracket_position VARCHAR(50), -- e.g., 'A1', 'B2', 'SF1'
    player1_id BIGINT REFERENCES users(id),
    player2_id BIGINT REFERENCES users(id),
    player1_score JSONB, -- {set1: 6, set2: 4, set3: 7}
    player2_score JSONB,
    winner_id BIGINT REFERENCES users(id),
    status tournament_match_status DEFAULT 'scheduled',
    scheduled_at TIMESTAMP,
    completed_at TIMESTAMP,
    metadata JSONB DEFAULT '{}'::jsonb,
    UNIQUE(tournament_id, round, match_number)
);

CREATE INDEX idx_tournament_matches_tournament ON tournament_matches(tournament_id);

-- ---------------------------
-- TOURNAMENT REGISTRATIONS
-- ---------------------------
CREATE TYPE registration_status AS ENUM ('pending', 'confirmed', 'cancelled', 'waitlisted');

CREATE TABLE tournament_registrations (
    id BIGSERIAL PRIMARY KEY,
    tournament_id BIGINT NOT NULL REFERENCES tournaments(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    status registration_status DEFAULT 'pending',
    seed_number INT,
    payment_id BIGINT REFERENCES payments(id),
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(tournament_id, user_id)
);

CREATE INDEX idx_tournament_registrations_tournament ON tournament_registrations(tournament_id);

-- ---------------------------
-- LESSONS (Private / Group coaching)
-- ---------------------------
CREATE TYPE lesson_status AS ENUM ('scheduled', 'completed', 'cancelled', 'no_show');
CREATE TYPE lesson_type AS ENUM ('private', 'group', 'clinic');

CREATE TABLE lessons (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    coach_id BIGINT NOT NULL REFERENCES coaches(id) ON DELETE CASCADE,
    court_id BIGINT REFERENCES courts(id) ON DELETE SET NULL,
    lesson_type lesson_type DEFAULT 'private',
    status lesson_status DEFAULT 'scheduled',
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    max_students INT DEFAULT 1,
    current_students INT DEFAULT 0,
    price_per_student DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    skill_level_min INT CHECK (skill_level_min BETWEEN 1 AND 7),
    skill_level_max INT CHECK (skill_level_max BETWEEN 1 AND 7),
    notes TEXT,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_lessons_club ON lessons(club_id);
CREATE INDEX idx_lessons_coach ON lessons(coach_id);
CREATE INDEX idx_lessons_status ON lessons(status);

-- ---------------------------
-- LESSON ENROLLMENTS
-- ---------------------------
CREATE TABLE lesson_enrollments (
    id BIGSERIAL PRIMARY KEY,
    lesson_id BIGINT NOT NULL REFERENCES lessons(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    payment_id BIGINT REFERENCES payments(id),
    status VARCHAR(50) DEFAULT 'confirmed',
    feedback TEXT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(lesson_id, user_id)
);

-- ---------------------------
-- NOTIFICATIONS
-- ---------------------------
CREATE TYPE notification_channel AS ENUM ('email', 'whatsapp', 'push', 'sms', 'in_app');
CREATE TYPE notification_status AS ENUM ('pending', 'sent', 'delivered', 'failed', 'read');

CREATE TABLE notifications (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    club_id BIGINT REFERENCES clubs(id) ON DELETE CASCADE,
    type VARCHAR(100) NOT NULL, -- 'booking_confirmed', 'reminder_24h', 'waitlist_available', etc
    channel notification_channel NOT NULL,
    status notification_status DEFAULT 'pending',
    subject VARCHAR(255),
    body TEXT NOT NULL,
    payload JSONB DEFAULT '{}'::jsonb,
    scheduled_at TIMESTAMP,
    sent_at TIMESTAMP,
    delivered_at TIMESTAMP,
    read_at TIMESTAMP,
    error_message TEXT,
    retry_count INT DEFAULT 0,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_notifications_status ON notifications(status);
CREATE INDEX idx_notifications_scheduled ON notifications(scheduled_at) WHERE status = 'pending';

-- ---------------------------
-- USER NOTIFICATION PREFERENCES
-- ---------------------------
CREATE TABLE notification_preferences (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    club_id BIGINT REFERENCES clubs(id) ON DELETE CASCADE,
    channel notification_channel NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, club_id, channel, event_type)
);

-- ---------------------------
-- REVIEWS / RATINGS
-- ---------------------------
CREATE TABLE reviews (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE NOT NULL,
    club_id BIGINT NOT NULL REFERENCES clubs(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    booking_id BIGINT REFERENCES bookings(id) ON DELETE SET NULL,
    coach_id BIGINT REFERENCES coaches(id) ON DELETE SET NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    is_visible BOOLEAN DEFAULT TRUE,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(club_id, user_id, booking_id)
);

CREATE INDEX idx_reviews_club ON reviews(club_id);
CREATE INDEX idx_reviews_coach ON reviews(coach_id);

-- ---------------------------
-- AUDIT LOG (optional, for compliance)
-- ---------------------------
CREATE TYPE audit_action AS ENUM ('create', 'update', 'delete', 'login', 'logout', 'payment', 'cancellation');

CREATE TABLE audit_logs (
    id BIGSERIAL PRIMARY KEY,
    club_id BIGINT REFERENCES clubs(id) ON DELETE CASCADE,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    action audit_action NOT NULL,
    entity_type VARCHAR(100) NOT NULL, -- 'booking', 'payment', 'user', etc
    entity_id BIGINT NOT NULL,
    old_values JSONB,
    new_values JSONB,
    ip_address VARCHAR(45),
    user_agent TEXT,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_audit_logs_club_entity ON audit_logs(club_id, entity_type, entity_id);
CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_created ON audit_logs(created_at);

-- ---------------------------
-- REFRESH MATERIALIZED VIEWS (for reporting)
-- ---------------------------
-- Example: daily court utilization per club
CREATE MATERIALIZED VIEW court_utilization_daily AS
SELECT
    c.club_id,
    c.id AS court_id,
    DATE(b.start_time) AS date,
    COUNT(b.id) AS booking_count,
    SUM(b.final_amount) AS revenue,
    SUM(EXTRACT(EPOCH FROM (b.end_time - b.start_time)) / 3600) AS hours_booked,
    COUNT(DISTINCT b.user_id) AS unique_players
FROM bookings b
JOIN courts c ON b.court_id = c.id
WHERE b.status NOT IN ('cancelled', 'no_show')
GROUP BY c.club_id, c.id, DATE(b.start_time);

CREATE UNIQUE INDEX idx_court_utilization_daily ON court_utilization_daily(club_id, court_id, date);

-- ---------------------------
-- SAMPLE DATA (optional, for testing)
-- ---------------------------
-- INSERT INTO clubs (name, slug, address, city, country, timezone) VALUES
-- ('Padel Club Jakarta', 'padel-club-jakarta', 'Jl. Sudirman No. 1', 'Jakarta', 'ID', 'Asia/Jakarta');

-- INSERT INTO users (email, phone, first_name, last_name, status) VALUES
-- ('admin@padelclub.id', '+6281234567890', 'Admin', 'User', 'active');

-- INSERT INTO club_members (club_id, user_id, role, status) VALUES
-- (1, 1, 'admin', 'active');

-- ============================================================
-- END OF SCHEMA
-- ============================================================
