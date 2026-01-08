# Conference Management System - User Workflow & Activity Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [User Roles & Permissions](#user-roles--permissions)
3. [Sidebar Navigation Menu](#sidebar-navigation-menu)
4. [Core Features & Modules](#core-features--modules)
5. [User Workflows](#user-workflows)
6. [User Activities](#user-activities)
7. [Authentication & Access](#authentication--access)
8. [Key Functionalities](#key-functionalities)

---

## System Overview

The Conference Management System is a comprehensive platform designed to manage conferences, participants, sessions, travel arrangements, and communications. The system supports multiple user roles with granular permission-based access control.

### Technology Stack
- **Framework**: Laravel (PHP)
- **Database**: MySQL
- **Frontend**: Blade Templates with Tailwind CSS
- **Authentication**: Laravel Auth with Passwordless Login
- **Email**: Gmail API Integration
- **Session Management**: File-based sessions with extended lifetime

---

## User Roles & Permissions

### 1. Super Admin
- **Permissions**: Full system access (`*` - all permissions)
- **Access**: Complete control over all modules and settings
- **Dashboard**: Main admin dashboard

### 2. Admin
- **Permissions**: Full access to core modules and administrative features
- **Key Capabilities**:
  - Manage conferences, participants, sessions
  - Access email tracking and settings
  - Manage backups
  - Handle travel itineraries
  - Generate passwordless login links
  - Manage Gmail integration
  - Bulk email operations

### 3. Organizer
- **Permissions**: Event operations and management
- **Key Capabilities**:
  - Create and edit conferences
  - Manage participants and sessions
  - Publish sessions
  - Create notifications
  - View travel itineraries and room allocations
  - Generate passwordless login links
  - Access conference documents

### 4. Speaker
- **Permissions**: View-only access to sessions and participants
- **Key Capabilities**:
  - View assigned sessions
  - View participant information
  - Access notifications
  - Download conference documents

### 5. Attendee
- **Permissions**: View-only access to sessions and participants
- **Key Capabilities**:
  - View sessions
  - View participant information
  - Access notifications
  - Download conference documents

---

## Sidebar Navigation Menu

The sidebar navigation menu is dynamically displayed based on user roles and permissions. The menu is collapsible and shows different items depending on the user's access level.

### Main Navigation Menu (Admin/Organizer/Super Admin)

The main sidebar menu includes the following items (displayed based on permissions):

1. **Dashboard** (`dashboard`)
   - Permission: Available to all authenticated users with roles
   - Description: Main dashboard with overview statistics and activities
   - Icon: Grid/List icon

2. **Users** (`users.index`)
   - Permission: `users.view`
   - Description: User account management
   - Icon: Users icon
   - Access: Admin/Super Admin only

3. **Roles** (`roles.index`)
   - Permission: `roles.view`
   - Description: Role and permission management
   - Icon: Shield/Key icon
   - Access: Admin/Super Admin only

4. **Conferences** (`conferences.index`)
   - Permission: `conferences.view`
   - Description: Conference management and creation
   - Icon: Calendar icon
   - Access: Admin, Organizer, and users with conference view permission

5. **Participants** (`participants.index`)
   - Permission: `participants.view`
   - Description: Participant management and profiles
   - Icon: Users group icon
   - Access: Admin, Organizer, and users with participant view permission

6. **Sessions** (`sessions.index`)
   - Permission: `sessions.view`
   - Description: Session management and scheduling
   - Icon: Briefcase/Calendar icon
   - Access: Admin, Organizer, and users with session view permission

7. **Itineraries** (`admin.itineraries`)
   - Permission: `travel.itineraries.view`
   - Description: Travel details and itinerary management
   - Icon: Map/Location icon
   - Access: Admin, Organizer, and users with travel itineraries view permission
   - Features:
     - View all participant travel details
     - Edit arrival/departure times
     - Manage hotel and room assignments
     - Session timing notes (3-hour window indicators)
     - CSV export functionality
     - Conference and participant filtering

8. **Notifications** (`notifications.index`)
   - Permission: `notifications.view`
   - Description: System notifications and alerts
   - Icon: Bell icon
   - Access: All authenticated users

9. **Conference Docs** (`conference-docs.index`)
   - Permission: `conference-docs.view`
   - Description: Conference document and media management
   - Icon: Document icon
   - Access: Admin, Organizer, and users with conference docs view permission

10. **Bulk Email** (`bulk.email`)
    - Permission: `bulk-email.view`
    - Description: Send emails to multiple participants
    - Icon: Envelope icon
    - Access: Admin and users with bulk email permission

11. **Backup Management** (`admin.backup.index`)
    - Permission: `backup.view`
    - Description: Database backup and restoration
    - Icon: Download/Upload icon
    - Access: Admin/Super Admin only

12. **Passwordless Login** (`passwordless-login.admin.index`)
    - Permission: `passwordless-login.admin.view`
    - Description: Generate and manage passwordless login links
    - Icon: Key icon
    - Access: Admin, Organizer, and users with passwordless login admin permission
    - Features:
      - Generate individual login links
      - Bulk link generation
      - Link expiration management
      - Usage tracking and statistics

13. **Email Tracking** (`admin.email-tracking.index`)
    - Permission: `email-tracking.view`
    - Description: Email delivery tracking and conversation management
    - Icon: Envelope with tracking icon
    - Access: Admin and users with email tracking permission
    - Features:
      - Email delivery status
      - Conversation threads
      - Email statistics
      - Resend functionality

14. **Gmail Conversations** (`gmail.index`)
    - Permission: `gmail.view`
    - Description: Direct Gmail integration and conversation management
    - Icon: Chat/Message icon
    - Access: Admin and users with Gmail permission
    - Features:
      - View Gmail inbox
      - Reply to emails
      - Search conversations
      - Manage email threads

### Settings Section (Admin/Super Admin Only)

The Settings section appears below a divider for Admin and Super Admin users:

15. **Email Template** (`admin.email-settings.index`)
    - Permission: Admin/Super Admin only
    - Description: Customize email templates and settings
    - Icon: Envelope/Edit icon
    - Features:
      - Edit email templates
      - Configure email variables
      - Preview templates
      - Test email sending

16. **Notification Template** (`admin.notification-templates.index`)
    - Permission: Admin/Super Admin only
    - Description: Customize notification templates
    - Icon: Bell/Edit icon
    - Features:
      - Edit notification templates
      - Configure notification variables
      - Preview templates

### Event Coordinator Menu

For users with the "event_coordinator" role, the sidebar shows:

1. **Event Coordinator Dashboard** (`event-coordinator.dashboard`)
   - Description: Event coordination dashboard
   - Icon: Grid icon

2. **Conferences** (`conferences.index`)
   - Description: Conference management
   - Icon: Calendar icon

3. **Participants** (`participants.index`)
   - Description: Participant management
   - Icon: Users group icon

4. **Sessions** (`sessions.index`)
   - Description: Session management
   - Icon: Briefcase icon

5. **Itineraries** (`event-coordinator.itineraries`)
   - Description: Travel itinerary management
   - Icon: Map icon

### Participant Menu

For participants (users without admin roles), the sidebar shows:

1. **My Profile** (`my-profile`)
   - Description: Personal participant profile
   - Icon: User icon
   - Features:
     - View assigned sessions
     - Session countdown timers
     - View other participants in sessions
     - Travel details
     - Profile management

2. **Participant Profiles** (`participant-profiles.index`)
   - Description: Manage multiple participant profiles
   - Icon: Users icon
   - Features:
     - Switch between profiles
     - Set primary profile
     - Archive/restore profiles

### Sidebar Features

- **Collapsible**: Sidebar can be collapsed/expanded
- **Responsive**: Automatically collapses on mobile devices
- **Permission-Based**: Menu items only show if user has required permissions
- **Active State**: Current page is highlighted
- **Tooltips**: Show menu item names when sidebar is collapsed
- **Smooth Transitions**: Animated expand/collapse with opacity transitions
- **Local Storage**: Sidebar state is saved in browser local storage

### Menu Item Visibility Rules

- Menu items are conditionally displayed based on:
  - User roles (superadmin, admin, organizer, speaker, attendee, event_coordinator)
  - Granular permissions (e.g., `conferences.view`, `participants.view`)
  - Permission wildcards (e.g., `conferences.*` grants all conference permissions)
  - Global wildcard (`*`) for superadmin (all permissions)

---

## Core Features & Modules

### 1. Conference Management
- Create, edit, and manage conferences
- Set conference dates, locations, and venues
- Conference conflict detection and resolution
- Export conference data

### 2. Participant Management
- Create and manage participant profiles
- Bulk import participants via CSV
- Participant type management (Speaker, Attendee, Organizer, etc.)
- Participant comments and notes
- Email communication with participants
- Participant profile management (multiple profiles per user)
- Profile conflicts resolution

### 3. Session Management
- Create and manage conference sessions
- Assign participants to sessions with roles (speaker, moderator, panelist, etc.)
- Session scheduling with start/end times
- Venue and room assignment
- Session capacity management
- Session publishing workflow
- Auto-save draft functionality
- Email notifications for session assignments
- Session export functionality

### 4. Travel & Itinerary Management
- Travel details management (arrival/departure dates)
- Hotel information and room allocations
- Room check-in/check-out times
- Flight information tracking
- Visa status management
- Itinerary status tracking
- Travel conflict detection
- CSV export of itineraries
- **Timing Notes**: Automatic notes showing if arrival/departure times are within 3 hours of session start/end times

### 5. Passwordless Login System
- Generate secure login links for participants
- Bulk link generation
- Token expiration management (default: 24-72 hours)
- Extended session lifetime (effectively never expires with "remember me")
- Link usage tracking
- Admin interface for link management
- Automatic email delivery of login links

### 6. Email Management
- **Gmail Integration**: Direct Gmail API integration
- **Email Tracking**: Complete email conversation tracking
- **Bulk Email**: Send emails to multiple participants
- **Email Templates**: Customizable email templates
- **Email Settings**: Configure email preferences
- **Conversation Threading**: Maintain conversation threads
- **Email Statistics**: Track email delivery and responses

### 7. Notification System
- In-app notifications
- Notification types (session assignments, system announcements, etc.)
- Mark as read/unread functionality
- Notification management dashboard

### 8. Conference Documents
- Upload and manage conference documents
- Media file management
- Document download functionality
- Document organization

### 9. Backup Management
- Database backup creation
- Backup restoration
- Backup cleanup
- Backup statistics

### 10. User & Role Management
- User account management
- Role creation and assignment
- Permission-based access control
- User activation/deactivation

### 11. Dashboard & Analytics
- Role-based dashboards
- Conference progress tracking
- Participant statistics
- Activity logs

---

## User Workflows

### Admin/Organizer Workflow: Conference Setup

1. **Create Conference**
   - Navigate to Conferences → Create New
   - Enter conference details (name, dates, location, venue)
   - Save conference

2. **Add Participants**
   - Navigate to Participants → Create New or Import CSV
   - Enter participant information
   - Assign participant type (Speaker, Attendee, etc.)
   - Set travel intent (national/international)
   - Save participant

3. **Create Sessions**
   - Navigate to Sessions → Create New
   - Enter session details (title, description, start/end time)
   - Assign venue and room
   - Set capacity
   - Save as draft or publish

4. **Assign Participants to Sessions**
   - Open session details
   - Add participants with roles (speaker, moderator, etc.)
   - System sends email notifications automatically

5. **Manage Travel Details**
   - Navigate to Itineraries
   - View all participant travel details
   - Edit arrival/departure times
   - Assign hotels and rooms
   - System shows timing notes for session alignment

6. **Generate Passwordless Login Links**
   - Navigate to Passwordless Login admin
   - Select participants or sessions
   - Generate links (individual or bulk)
   - Links are automatically emailed to participants

### Participant Workflow: Accessing Conference Information

1. **Receive Login Link**
   - Participant receives email with passwordless login link
   - Link expires after configured time (24-72 hours typically)

2. **Access System**
   - Click login link
   - Automatically logged in (no password required)
   - Session persists with "remember me" functionality

3. **View Profile**
   - Access "My Profile" page
   - View assigned sessions
   - See session countdown timers
   - View other participants in sessions
   - Check travel details

4. **View Sessions**
   - See all assigned sessions
   - View session details (time, location, room)
   - See other participants in each session
   - Access session documents

5. **Manage Multiple Profiles**
   - Switch between participant profiles if user has multiple
   - Set primary profile
   - Archive/restore profiles

### Email Communication Workflow

1. **Send Email to Participants**
   - Navigate to Bulk Email or Email Tracking
   - Select participants or sessions
   - Compose email message
   - Send email via Gmail API

2. **Track Email Conversations**
   - View email tracking dashboard
   - See conversation threads
   - Reply to emails directly from system
   - Track email delivery and responses

---

## User Activities

### Daily Activities

#### Admin/Organizer Activities
- **Morning**:
  - Check dashboard for new notifications
  - Review conference progress
  - Check for travel conflicts
  - Review pending items

- **During Day**:
  - Create/edit sessions
  - Assign participants to sessions
  - Manage travel itineraries
  - Respond to participant emails
  - Generate passwordless login links as needed
  - Upload conference documents

- **Evening**:
  - Review email tracking statistics
  - Check for session conflicts
  - Review system status
  - Review participant comments

#### Participant Activities
- **Login**: Access system via passwordless login link
- **View Sessions**: Check assigned sessions and schedules
- **View Profile**: Review personal information and travel details
- **Check Notifications**: View system notifications
- **Download Documents**: Access conference materials

### Weekly Activities

#### Admin/Organizer
- **Monday**: Review upcoming week's sessions and participants
- **Mid-week**: Generate passwordless login links for new participants
- **Friday**: Export reports (conferences, participants, sessions)
- **Weekend**: Review travel arrangements and conflicts

### Event-Specific Activities

#### Pre-Event
- Create conference and sessions
- Import/register participants
- Assign participants to sessions
- Set up travel arrangements
- Generate passwordless login links
- Send welcome emails
- Upload conference documents

#### During Event
- Monitor session attendance
- Handle real-time updates
- Manage travel changes
- Send session reminders
- Update session information

#### Post-Event
- Export participant data
- Generate reports
- Archive conference data
- Review email communications
- Clean up expired login links

---

## Authentication & Access

### Standard Authentication
- **Login**: Email and password
- **Session Lifetime**: 120 minutes (2 hours) by default
- **Remember Me**: Available for extended sessions

### Passwordless Authentication
- **Method**: Secure token-based login links
- **Token Expiration**: 
  - Default: 24 hours
  - Session invitations: 72 hours
  - Custom: Based on conference end date or explicit expiration
- **Session Lifetime**: Extended (effectively never expires with "remember me")
- **Security**: 
  - SHA-256 hashed tokens
  - Single-use tracking
  - IP address and user agent logging
  - Rate limiting (5 requests per hour per user)

### Access Control
- **Permission-Based**: Granular permissions per module
- **Role-Based**: Roles define permission sets
- **Middleware Protection**: Routes protected by permission checks
- **Dashboard Routing**: Users redirected to appropriate dashboard based on role

---

## Key Functionalities

### 1. Session-Participant Timing Notes
- **Feature**: Automatic notes in itineraries table
- **Logic**: 
  - Arrival time within 3 hours before/after session start → Shows note
  - Departure time within 3 hours before/after session end → Shows note
- **Display**: Blue badge with info icon showing time difference
- **Purpose**: Help identify potential scheduling conflicts or tight timing

### 2. Participant Profile Management
- **Multiple Profiles**: Users can have multiple participant profiles
- **Profile Switching**: Switch between profiles for different conferences
- **Primary Profile**: Set one profile as primary
- **Conflict Detection**: System detects profile conflicts
- **Archive/Restore**: Archive unused profiles

### 3. Bulk Operations
- **Bulk Import**: CSV import for participants
- **Bulk Email**: Send emails to multiple participants
- **Bulk Session Assignment**: Assign multiple participants to sessions
- **Bulk Login Link Generation**: Generate links for multiple users

### 4. Email Integration
- **Gmail API**: Direct integration with Gmail
- **Conversation Threading**: Maintains email threads
- **Email Tracking**: Tracks all sent emails
- **Reply Management**: Reply to emails from system interface
- **Template System**: Customizable email templates

### 5. Conference Conflict Detection
- **Date Overlap**: Detects overlapping conference dates
- **Conflict Resolution**: Tools to resolve conflicts
- **Conflict Management**: Admin interface for conflict handling

### 6. Travel Management
- **Itinerary Tracking**: Complete travel details management
- **Hotel Assignment**: Assign hotels and rooms
- **Room Allocation**: Manage room check-in/check-out
- **Travel Conflicts**: Detect room and hotel conflicts
- **Export**: CSV export of all travel details

### 7. Notification System
- **Real-time Notifications**: In-app notification system
- **Notification Types**: 
  - Session assignments
  - System announcements
  - Email notifications
- **Read/Unread Status**: Track notification status
- **Notification Management**: Mark all as read, individual management

### 8. Dashboard Analytics
- **Conference Progress**: Track conference completion
- **Participant Statistics**: View participant counts and types
- **Activity Logs**: View system activities
- **Summary Statistics**: Overview of key metrics

### 9. Document Management
- **File Upload**: Upload conference documents
- **Media Management**: Manage images and files
- **Download Tracking**: Track document downloads
- **Organization**: Organize documents by conference

### 10. Backup & Recovery
- **Automated Backups**: Schedule database backups
- **Manual Backups**: Create backups on demand
- **Backup Restoration**: Restore from backups
- **Backup Cleanup**: Manage backup storage
- **Backup Statistics**: View backup history

---

## System Configuration

### Session Configuration
- **Lifetime**: 5,256,000 minutes (~10 years) for passwordless logins
- **Remember Me**: Enabled for passwordless authentication
- **Driver**: File-based sessions

### Email Configuration
- **Provider**: Gmail API
- **Templates**: Customizable email templates
- **Tracking**: Complete email tracking and conversation management

### Permission System
- **Granular Permissions**: Module.action format (e.g., `conferences.view`)
- **Wildcard Permissions**: Module.* for all actions in module
- **Global Wildcard**: * for all permissions (superadmin)

---

## Data Models & Relationships

### Core Models
- **User**: System users with roles and permissions
- **Conference**: Conference events
- **Participant**: Conference participants (linked to users)
- **Session**: Conference sessions
- **TravelDetail**: Travel and itinerary information
- **PasswordlessLogin**: Passwordless login tokens
- **Notification**: System notifications
- **Email**: Email tracking and conversations

### Key Relationships
- User → Participants (one-to-many)
- Conference → Participants (one-to-many)
- Conference → Sessions (one-to-many)
- Participant → Sessions (many-to-many with roles)
- Participant → TravelDetail (one-to-one)
- User → PasswordlessLogin (one-to-many)
- User → Roles (many-to-many)

---

## API Endpoints

### Public Endpoints
- Passwordless login verification: `/passwordless-login/verify/{token}`
- Google OAuth callback: `/google-callback`

### Authenticated Endpoints
- Dashboard data: `/dashboard/data`
- Conference progress: `/dashboard/conference-progress`
- Participant stats: `/dashboard/participant-stats`
- Activities: `/dashboard/activities`

### Admin Endpoints
- Passwordless login admin: `/admin/passwordless-login`
- Email tracking: `/admin/email-tracking`
- Travel itineraries: `/admin/itineraries`
- Backup management: `/admin/backup`

---

## Security Features

1. **Permission-Based Access Control**: All routes protected by permissions
2. **Role-Based Routing**: Users redirected based on roles
3. **Token Security**: SHA-256 hashed tokens for passwordless login
4. **Rate Limiting**: Prevents abuse of login link generation
5. **Session Security**: Secure session management
6. **CSRF Protection**: Laravel CSRF token protection
7. **Input Validation**: Comprehensive validation on all inputs

---

## Reporting & Export

### Available Exports
- Conference data (CSV)
- Participant data (CSV)
- Session data (CSV)
- Itinerary data (CSV)
- Email tracking data

### Reports
- Conference progress reports
- Participant statistics
- Email delivery statistics
- Travel conflict reports

---

## Maintenance & Administration

### Regular Maintenance Tasks
- Clean up expired passwordless login tokens
- Archive old conferences
- Backup database regularly
- Review and resolve travel conflicts
- Monitor email delivery rates
- Review system logs

### Administrative Tasks
- User management (create, edit, activate/deactivate)
- Role and permission management
- Email template customization
- System configuration
- Backup management

---

## Support & Documentation

### Additional Documentation Files
- `GMAIL_SETUP_GUIDE.md`: Gmail integration setup
- `BULK_IMPORT_GUIDE.md`: Participant bulk import guide
- `BULK_IMPORT_IMPLEMENTATION_SUMMARY.md`: Import implementation details

### System Features Summary
- Multi-role user management
- Conference and session management
- Participant management with bulk import
- Travel and itinerary management
- Passwordless authentication
- Gmail integration
- Email tracking and conversation management
- Notification system
- Document management
- Backup and recovery
- Comprehensive reporting and export

---

*Last Updated: Based on current codebase analysis*
*Version: 1.0*

