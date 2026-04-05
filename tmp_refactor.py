import os
import re

auth_files = {"customer_login.php", "customer_register.php", "staff_login.php", "staff_register.php", "admin_login.php"}
action_files = {"apply_loan.php", "customer_support.php", "approve_staff.php", "reject_staff.php"}
logout_files = {"admin_logout.php", "clerk_logout.php", "customer_logout.php", "loanofficer_logout.php", "manager_logout.php"}
frontend_files = {"index.html", "customer_login.html", "customer_register.html", "customer_support.html", "apply_loan.html", "staff_login.html", "staff_register.html", "admin_dashboard.php", "admin_view_report.php", "customer_dashboard.php", "customer_profile.php", "staff_dashboard.php", "clerk_manage_customerrequest.php", "clerk_data_entry.php", "loan_officer_review.php", "loanofficer_customer_loan.php", "manage_customers.php", "manage_managers.php", "manage_officers.php", "manager_loan_approval.php", "manager_reports.php", "my_loans.php", "manage_staff.php"}


db_blocks = [
    re.compile(r'// Database connection.*?mysqli_connect_error\(\)\);\s*\}', re.DOTALL),
    re.compile(r'\$servername\s*=\s*"[^"]+";.*\$conn\s*=\s*mysqli_connect\([^)]+\);(?:\s*if\s*\(!\$conn\)\s*\{[^\}]+\})?', re.DOTALL)
]

def replace_links(content, is_frontend=True):
    # Form actions
    def form_replacer(match):
        action = match.group(2)
        if action in auth_files:
            return match.group(1) + '"../backend/auth/' + action + '"'
        elif action in action_files:
            return match.group(1) + '"../backend/actions/' + action + '"'
        elif action in frontend_files:
            return match.group(1) + '"' + action + '"'
        return match.group(0)

    content = re.sub(r'(action\s*=\s*)["\'](.*?)["\']', form_replacer, content)

    # href links
    def href_replacer(match):
        action = match.group(2)
        # ignore mailto, tel, http
        if action.startswith('http') or action.startswith('mailto') or action.startswith('tel'):
            return match.group(0)
        
        if action in logout_files:
            return match.group(1) + '"../backend/logout/' + action + '"'
        elif action in action_files:
            return match.group(1) + '"../backend/actions/' + action + '"'
        elif action in auth_files:
            return match.group(1) + '"../backend/auth/' + action + '"'
        elif action in frontend_files:
            return match.group(1) + '"' + action + '"'
        return match.group(0)

    content = re.sub(r'(href\s*=\s*)["\'](.*?(?:\.php|\.html).*?)["\']', href_replacer, content)

    # headers
    def header_replacer(match):
        location = match.group(1)
        if '?' in location:
            base, qs = location.split('?', 1)
        else:
            base, qs = location, ""
        
        if not is_frontend:
            # from backend to frontend
            if base in frontend_files:
                base = "../../frontend/" + base
            elif base in auth_files:
                base = "../auth/" + base
            elif base in action_files:
                base = "../actions/" + base
        else:
            # from frontend to frontend
            if base in frontend_files:
                pass # keeps the same
                
        if qs:
             location = base + "?" + qs
        else:
             location = base
        return 'header("Location: ' + location + '")'

    content = re.sub(r'header\("Location:\s*([^"]+)"\)', header_replacer, content)
    content = re.sub(r'header\(\'Location:\s*([^\']+)\'\)', header_replacer, content)
    
    return content

# process frontend files
for root, dirs, files in os.walk('frontend'):
    for file in files:
        if file.endswith('.php') or file.endswith('.html'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            # replace DB
            for p in db_blocks:
                 content = p.sub("require_once '../backend/config.php';", content)
                 
            content = replace_links(content, True)
            
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)

# process backend files
for root, dirs, files in os.walk('backend'):
    for file in files:
        if file.endswith('.php') and file != 'config.php':
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            # replace DB
            for p in db_blocks:
                 content = p.sub("require_once '../../config.php';", content)
                 
            content = replace_links(content, False)
            
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
                
print('Done frontend and backend replacements.')
