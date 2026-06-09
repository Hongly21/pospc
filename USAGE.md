# POS System Usage Guide

This manual explains how to use the POS system from the frontend. It covers every major feature, the navigation flow, and the exact steps for the common tasks your staff and managers perform.

## 1. System Overview and User Interface

The application is built with Laravel Blade templates and standard Bootstrap layout. The main interface is composed of:

- **Header / top bar**: user profile, notifications, language switch, and quick actions.
- **Sidebar menu**: primary navigation links, organized by role and modules.
- **Main content area**: page headings, search and filter forms, tables, cards, and action buttons.
- **Modals and forms**: used for creating or editing data without leaving the current page.

### Layout conventions

- Pages have a title at the top and a descriptive header.
- Most listing pages include a search/filter form above the results table.
- Action buttons are placed in the page header or directly inside the table rows.
- Modals are used for add/edit workflows to keep the user context intact.
- Pagination controls appear at the bottom of any large list.

> This structure is repeated in the frontend so users can quickly learn where to search and where to act.

---

## 2. User Roles and Permissions

The system uses role-based access control. Available roles include:

- **Admin**: full access to all features, including dashboard, users, settings, reports, products, suppliers, purchases, inventory, expenses, and POS.
- **Manager**: access to most business operations, including POS, customers, products, inventory, suppliers, purchases, and reports.
- **Staff**: primarily POS and customer selection, with limited access to product lookup.

### Role behavior in the frontend

- The sidebar only shows pages your role can open.
- Attempting to access restricted pages will be blocked.
- Admins can approve and manage user accounts.

> If a menu item is missing, request the correct role from your Admin.

---

## 3. Authentication Pages

### Login page

This page includes:

- Email or username field
- Password field
- Login button
- Forgot password link
- Register link

### Register page

Used to create a new account. The frontend includes fields for:

- Full name
- Email
- Password and password confirmation
- A hidden default role (typically Staff)

> New accounts may need Admin approval before full access is granted.

### Forgot password

This page sends an OTP to the user email. The frontend fields include:

- Email address field
- Submit button

### Reset password

After receiving an OTP, the reset page includes:

- Hidden email field
- OTP field
- New password and password confirmation fields
- Submit button

> These pages are front-end forms that post to Laravel routes for authentication and password recovery.

---

## 4. Main Frontend Features

Below are the main frontend pages and how to use them.

### 4.1 Dashboard

**Where to find it**: sidebar > Dashboard

**What it shows**:

- Business summary cards: total sales, orders, profit, expenses, recent activity.
- Charts and tables to review performance.
- Alerts about low stock, overdue debt, or other issues.

**How to use it**:

1. Open the Dashboard after login.
2. Review the summary cards for current performance.
3. Use charts to identify trends.
4. Click into linked pages if you need more detail.

**Why it matters**:

The dashboard is the business command center. Use it every day to confirm everything is healthy.

### 4.2 POS Terminal

**Where to find it**: sidebar > POS

**Page sections**:

- Product search area
- Product cards or list for selection
- Cart summary on the right
- Customer selection panel
- Payment modal and buttons

**How to use it**:

1. Search for products by name or barcode.
2. Add items to the cart by clicking them.
3. Adjust quantity inside the cart if needed.
4. Select a registered customer or leave blank for walk-in sales.
5. Choose payment type:
   - Cash
   - Card
   - KHQR (if enabled)
6. Click **Complete Sale**.
7. Print or download the receipt from the receipt page.

**Frontend behavior**:

- The cart updates immediately on screen.
- Selected payment type may show additional options or QR payment UI.
- Cart totals recalculate automatically.

**Why it matters**:

This is the most important front-end workflow. It directly supports revenue generation and customer checkout.

### 4.3 Sales History

**Where to find it**: sidebar > Sales History

**Page sections**:

- Search/filter row
- Table of historical orders
- Order details and status badges
- Pay Debt modal for customer balances

**How to use it**:

1. Enter search terms or filters to find an order.
2. Use the status filter to narrow to paid, pending, or debt orders.
3. Click an order to see details or open the receipt.
4. If a customer owes money, use the **Pay Debt** button.

**Why it matters**:

Sales history lets you audit transactions, manage payment collection, and verify past sales.

### 4.4 Products

**Where to find it**: sidebar > Products

**Page sections**:

- Search bar and filters
- Product list table
- Add Product button
- Edit/Delete actions per row
- Add Product modal

**How to use it**:

1. Search by product name, category, or code.
2. Filter by category or status.
3. Click **Add Product** to create a new item.
4. Fill in the product form: name, category, price, cost, stock, SKU, and status.
5. Save the product.
6. Edit existing products from the table row actions.
7. Delete only if the product is no longer needed.

**Why it matters**:

Product data powers the POS and inventory. Clear item details keep checkout accurate and stock tracking reliable.

### 4.5 Categories

**Where to find it**: sidebar > Categories

**Page sections**:

- Category table
- Search / filter
- Add Category modal

**How to use it**:

1. Click **Add Category**.
2. Enter the category name and description.
3. Save it.
4. Use categories when adding or editing products.

**Why it matters**:

Categories make the product catalog easier to navigate, both for staff and reports.

### 4.6 Inventory

**Where to find it**: sidebar > Inventory

**Page sections**:

- Search and filter form
- Current stock table
- Update button or inline controls
- Inventory history link

**How to use it**:

1. Search or filter products by stock level, category, or name.
2. Review current quantities and reorder levels.
3. Click update to adjust stock quantities or reorder thresholds.
4. Open **Inventory History** to audit stock changes.

**Why it matters**:

Inventory is the foundation of retail operations. Monitoring stock levels prevents overselling and helps plan purchases.

### 4.7 Inventory History

**Where to find it**: sidebar > Inventory > History

**Page sections**:

- Date filters and search
- Stock change history table
- Pagination controls

**How to use it**:

1. Set a date range to view changes for that period.
2. Search by product name or action type.
3. Review each entry to understand stock adjustments.

**Why it matters**:

History provides traceability for stock corrections, receipts, and adjustments.

### 4.8 Suppliers

**Where to find it**: sidebar > Suppliers

**Page sections**:

- Search/filter form
- Supplier list table
- Add Supplier modal
- Edit/Delete actions

**How to use it**:

1. Click **Add Supplier**.
2. Add supplier name, phone, email, and address.
3. Save the supplier profile.
4. Edit supplier details as needed.

**Why it matters**:

Suppliers are essential for maintaining inventory supply and purchase records.

### 4.9 Purchases

**Where to find it**: sidebar > Purchases

**Page sections**:

- Filter/search row
- Purchase history list
- Add Purchase button
- Purchase form with product rows
- Total spent summary card

**How to use it**:

1. Click **Purchase** to create a new stock entry.
2. Select the supplier and purchase date.
3. Add one or more items with quantities and unit costs.
4. Save the purchase.
5. Review the **Total Spent** summary for the selected period.

**Why it matters**:

Purchasing records show stock inflow and cost history. This is needed for proper inventory valuation and expense tracking.

### 4.10 Customers

**Where to find it**: sidebar > Customers

**Page sections**:

- Customer search and filters
- Table of customer records
- Add Customer modal
- Status and debt filters

**How to use it**:

1. Click **Add New Customer** to capture customer details.
2. Enter name, phone, email, and address.
3. Use filters to view customers with balances or specific statuses.
4. Edit customer data from row actions.

**Why it matters**:

Customer management supports loyalty, credit sales, and personalized service.

### 4.11 Taxes

**Where to find it**: sidebar > Taxes

**Page sections**:

- Tax rate list
- Add Tax modal
- Edit/Delete actions

**How to use it**:

1. Click **Add Tax** to configure a new tax line.
2. Provide the tax name, rate, and effective status.
3. Save the tax rule.
4. Use tax settings in products and sales calculations.

**Why it matters**:

Taxes must be applied correctly at checkout and in financial reports.

### 4.12 Expenses

**Where to find it**: sidebar > Expenses

**Page sections**:

- Expense search and filters
- Expense list table
- Add Expense modal

**How to use it**:

1. Click **Record New Expense**.
2. Input the expense category, amount, date, and description.
3. Save the record.
4. Review expense totals and filter by date.

**Why it matters**:

Expenses are required to calculate net profit and manage cash flows.

### 4.13 Reports

**Where to find it**: sidebar > Reports > Sales

**Page sections**:

- Date range filters
- Summary cards for orders count, sales, COGS, profit, received amount, and debt
- Detailed sales history table
- Print and export options if available

**How to use it**:

1. Choose start and end dates.
2. Apply filters and submit the report form.
3. Review summary figures.
4. Use the table for transaction-level detail.

**Why it matters**:

Reports provide insight into performance, profitability, and customer behavior.

### 4.14 Settings

**Where to find it**: sidebar > Settings

**Page sections**:

- Shop name field
- Phone and address fields
- Save changes button

**How to use it**:

1. Update the shop name, phone, and address.
2. Save the form.
3. Verify the changes in receipts and page headers.

**Why it matters**:

Correct store settings ensure all printed and emailed documents show the right business identity.

---

## 5. Example Workflows

### 5.1 Create a new sale

1. Open the **POS** page.
2. Search for the first product.
3. Add it to the cart.
4. Repeat for additional items.
5. Select a customer if available.
6. Choose a payment type.
7. Click **Complete Sale**.
8. Print or save the receipt.

### 5.2 Add a new product

1. Open the **Products** page.
2. Click **Add Product**.
3. Fill in name, category, price, cost, and stock.
4. Click **Save**.
5. Confirm the new item appears in the product list.

### 5.3 Record inventory arrival

1. Open **Purchases**.
2. Click **Purchase**.
3. Choose the supplier.
4. Add items and quantities.
5. Submit the purchase.
6. Use **Inventory** to verify quantities updated.

### 5.4 Collect customer debt

1. Open **Sales History**.
2. Search for the customer or order.
3. Click **Pay Debt** for the selected order.
4. Enter the payment details.
5. Submit to reduce the outstanding balance.

---

## 6. Frontend Tips and Best Practices

- Always use the search and filter fields before editing.
- Double-check product quantities and prices before saving.
- Confirm customer details when applying a balance or credit.
- Use modals to keep the current page context.
- After saving, look for success messages or alerts.
- Refresh lists with pagination when table results are large.
- Logout at the end of the day.

### If a frontend element does not work

- Refresh the page.
- Check your role access.
- Contact an Admin for permissions.
- Report the issue with the page name and the action you attempted.

---

## 7. Glossary of Common Terms

- **POS**: Point of Sale, the checkout interface.
- **Category**: Product grouping used for filtering and reporting.
- **Inventory**: Current stock quantities and reorder information.
- **Supplier**: The vendor providing inventory.
- **Purchase**: A stock entry received from a supplier.
- **Expense**: Business cost recorded in the system.
- **Debt**: Outstanding amount owed by a customer.
- **KHQR**: QR payment method used in the POS checkout.
- **OTP**: One-time password used for password reset.

---

## 8. Need Help?

If you need support:

- Ask your Admin for role and permission issues.
- Report bugs with the page and action details.
- Use the dashboard to identify gaps in your data.
- Keep this guide nearby for reference.

Thank you for using the POS system. This guide is intended to help you use the frontend confidently and complete every major task correctly.
