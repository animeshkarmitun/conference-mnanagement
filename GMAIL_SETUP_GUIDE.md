# Gmail Integration Setup Guide

## Your Admin Account
- **Email:** conferencescgs@gmail.com
- **Password:** AdminPassword123!
- **Role:** Admin

## Step 1: Login to the System
1. Go to your conference management system
2. Login with:
   - Email: `conferencescgs@gmail.com`
   - Password: `AdminPassword123!`

## Step 2: Connect Gmail
1. After logging in, go to: `/google/redirect`
2. You'll be redirected to Google's authorization page
3. Sign in with your Gmail account: `conferencescgs@gmail.com`
4. Grant permissions for the application to access Gmail
5. You'll be redirected back to the system

## Step 3: Test Gmail Integration
Run this command to test the Gmail integration:

```bash
php artisan test:gmail-real johnleohere@gmail.com
```

This will:
- Send a real email from `conferencescgs@gmail.com` to `johnleohere@gmail.com`
- Track the email in the database
- Show conversation statistics

## Step 4: View Conversations
1. Go to `/admin/email-tracking`
2. Switch to "Conversations" view
3. Select `johnleohere@gmail.com` from the dropdown
4. You'll see the complete conversation history

## Step 5: Send Conference Updates
To send updates to all conference participants:

```bash
php artisan conference:send-update 1 "Important Update" "Conference details have changed"
```

## Features Available After Gmail Setup:

### 1. Real Email Sending
- All system emails sent via Gmail API
- Emails appear in your Gmail sent folder
- Thread management for conversations

### 2. Gmail Conversations Menu
- View Gmail inbox directly in the system
- Reply to emails from the system interface
- Search Gmail conversations

### 3. Email Tracking Dashboard
- Complete conversation history
- Email statistics and analytics
- Participant communication management

### 4. Conference Updates
- Send bulk emails to all participants
- Track delivery and responses
- Maintain conversation threads

## Troubleshooting

### If Gmail connection fails:
1. Check Google Cloud Console settings
2. Ensure Gmail API is enabled
3. Verify OAuth credentials
4. Check redirect URI matches

### If emails don't send:
1. Verify Gmail token is valid
2. Check Gmail API quotas
3. Ensure participant email is valid
4. Check system logs for errors

## Test Commands

```bash
# Test conversation system
php artisan test:conversation-system johnleohere@gmail.com

# Test Gmail integration
php artisan test:gmail-real johnleohere@gmail.com

# Send conference update
php artisan conference:send-update 1 "Subject" "Message"
```

## Next Steps
1. Complete Gmail setup
2. Test with real emails
3. Set up Gmail webhooks for incoming emails
4. Train team on new features
5. Monitor email delivery and responses

























