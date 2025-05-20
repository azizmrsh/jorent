# Contract Factory and Seeder Implementation

This document explains how to use the ContractFactory and ContractSeeder to generate test data for your contracts.

## Contract Factory

The `ContractFactory` has been set up to create realistic contract data with:

- Random landlord names
- Relationships to tenants, units, and properties
- Realistic start, end and due dates (current date-based)
- Random rent amounts
- Terms and conditions
- Various contract statuses (active, pending, completed, terminated)
- Digital signatures
- Hire information

The factory also includes state methods to create contracts with specific statuses:
- `active()` - Creates contracts with "active" status
- `pending()` - Creates contracts with "pending" status

## Contract Seeder

The `ContractSeeder` will:
1. Check if dependent models exist (Tenant, Unit, Property)
2. Create these models if they don't exist
3. Generate 20 contracts total with different statuses:
   - 10 random contracts
   - 5 active contracts
   - 3 pending contracts
   - 2 completed contracts

## Database Seeder

The `DatabaseSeeder` has been updated to run seeders in the proper order, 
ensuring dependencies are respected (ContractSeeder runs after TenantSeeder, PropertySeeder, and UnitSeeder).

## Running the Seeders

To run only the ContractSeeder:

```bash
php artisan db:seed --class=Database\\Seeders\\ContractSeeder
```

To run all seeders (including the ContractSeeder):

```bash
php artisan db:seed
```

## Additional Notes

- The seeder checks for existing dependencies and creates them if needed
- The factory generates realistic date ranges for contracts
- Random selection of statuses ensures a diverse dataset
- Custom states allow generating specific types of contracts when needed
