-- Database Assessment Queries
-- Timestamp: 2025-02-25 19:30:15
-- User: niloc95

-- 1. Get database size and table information
SELECT 
    table_schema as 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as 'Size (MB)',
    COUNT(*) as 'Number of Tables'
FROM information_schema.tables
WHERE table_schema = 'webschedulr'
GROUP BY table_schema;

-- 2. Get detailed table information
SELECT 
    table_name as 'Table',
    ROUND((data_length + index_length) / 1024 / 1024, 2) as 'Size (MB)',
    table_rows as 'Approximate Rows',
    CREATE_TIME as 'Created',
    UPDATE_TIME as 'Last Updated'
FROM information_schema.tables
WHERE table_schema = 'webschedulr'
ORDER BY (data_length + index_length) DESC;

-- 3. Check for foreign key relationships
SELECT 
    table_name as 'Table',
    column_name as 'Column',
    referenced_table_name as 'References Table',
    referenced_column_name as 'References Column'
FROM information_schema.key_column_usage
WHERE referenced_table_name IS NOT NULL
    AND table_schema = 'webschedulr';