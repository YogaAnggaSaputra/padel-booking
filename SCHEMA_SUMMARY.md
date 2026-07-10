# Padel Booking Platform — Schema Summary

## Total Tables: 33
## Total Views: 1 (materialized)

---

## Core Tenant
| Table | Records | Purpose |
|-------|---------|---------|
| `clubs` | clubs | Multi-tenant master — every entity scoped to club_id |

## User Management (6 tables)
| Table | Records | Purpose |
|-------|---------|---------|
| `users` | users | Platform-wide users (player, coach, admin) |
| `user_social_accounts` | social_accounts | Google/Apple/SSO auth links |
| `club_members` | club_members | Many-to-many user ↔ club with role |
| `notification_preferences` | notification_prefs | Per-user per-channel opt-in/out |
| `reviews` | reviews | Ratings for clubs, coaches, courts |
| `audit_logs` | audit_logs | Compliance trail |

## Court Management (3 tables)
| Table | Records | Purpose |
|-------|---------|---------|
| `courts` | courts | Per-club court definition + pricing base |
| `court_availability` | court_availability | Blocked/special hours/maintenance overrides |
| `court_pricing` | court_pricing | Dynamic pricing per day+time slot |

## Booking Engine (5 tables)
| Table | Records | Purpose |
|-------|---------|---------|
| `bookings` | bookings | Main entity — all reservation types |
| `booking_participants` | booking_participants | Split payment / guest players |
| `waitlist_entries` | waitlist_entries | Auto-fill cancelled slots |
| `payments` | payments | All payment transactions |
| `equipment_rentals` | equipment_rentals | Racket/ball rental tied to booking |

## Financial (3 tables)
| Table | Records | Purpose |
|-------|---------|---------|
| `user_credits` | user_credits | Wallet ledger |
| `user_memberships` | user_memberships | Active/past memberships |
| `membership_transactions` | membership_transactions | Purchase/renewal/upgrade history |
| `membership_plans` | membership_plans | Subscription plan definitions |

## Coaching (4 tables)
| Table | Records | Purpose |
|-------|---------|---------|
| `coaches` | coaches | Coach profile per club |
| `coach_availability` | coach_availability | Teaching schedule |
| `lessons` | lessons | Private/group/clinic sessions |
| `lesson_enrollments` | lesson_enrollments | Student enrollment in lessons |

## Matchmaking & Tournaments (5 tables)
| Table | Records | Purpose |
|-------|---------|---------|
| `matches` | matches | Open games for matchmaking |
| `match_participants` | match_participants | Players in a match |
| `tournaments` | tournaments | Tournament/league definitions |
| `tournament_matches` | tournament_matches | Bracket/scheduled tournament matches |
| `tournament_registrations` | tournament_registrations | Player registrations |

## Equipment (2 tables)
| Table | Records | Purpose |
|-------|---------|---------|
| `equipment` | equipment | Rentable items inventory |
| `equipment_rentals` | equipment_rentals | Rental instances tied to bookings |

## Notifications (1 table)
| Table | Records | Purpose |
|-------|---------|---------|
| `notifications` | notifications | All outbound messages (queue) |

---

## Key Design Decisions

### 1. Generated Columns
- `users.full_name` — auto-concatenated from first_name + last_name
- `bookings.duration_minutes` — computed from start/end timestamps

### 2. JSONB Everywhere
- `metadata` field on every table for extensibility (payment response, coach certs, rules)
- `players` JSONB on bookings for inline player tracking without extra joins

### 3. Multi-Tenant via club_id
- Every data table has `club_id` with FK; no shared row risk between clubs
- Queries always scoped: `WHERE club_id = ?`

### 4. Recurring Bookings
- `recurring_group_uuid` + `recurring_rule` (iCal RRULE) — handles daily/weekly/monthly patterns
- Individual occurrences are rows in `bookings` so cancel/edit one doesn't affect others

### 5. Pricing Strategy
- `courts.base_price` + optional `peak_price` / `off_peak_price` for simple tiering
- `court_pricing` table for complex dynamic rules (e.g. Monday 18-21: 40% premium)

### 6. Split Payments
- `booking_participants.share_amount` + `payment_status`
- Each participant can pay independently from their wallet/credit

### 7. Reports
- Materialized view `court_utilization_daily` auto-refreshable
- All bookings have `checked_in_at` / `checked_out_at` for real-time occupancy tracking

---

## Migration Strategy (Laravel)

Model naming convention:
- `App\Models\Club`, `App\Models\Court`, `App\Models\Booking`, etc.
- Each has `HasUuid` trait, `BelongsToClub` concern
- BookingController scoped: `$club->bookings()->whereDate('start_time', $request->date)`

## Files Generated

```
/home/ngodingyuk/padel-booking/
├── schema.sql      (33 tables + 1 materialized view, full DDL)
└── erd.mermaid     (ERD diagram in mermaid format)
```
