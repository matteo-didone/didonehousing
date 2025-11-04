-- ============================================================================
-- PostgreSQL Initialization - Extensions
-- ============================================================================

-- Enable PostGIS for geospatial queries
CREATE EXTENSION IF NOT EXISTS postgis;
CREATE EXTENSION IF NOT EXISTS postgis_topology;

-- Enable pg_trgm for fuzzy text search
CREATE EXTENSION IF NOT EXISTS pg_trgm;

-- Enable uuid-ossp for UUID generation
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Enable btree_gist for exclusion constraints
CREATE EXTENSION IF NOT EXISTS btree_gist;

-- Enable unaccent for accent-insensitive search
CREATE EXTENSION IF NOT EXISTS unaccent;

-- Create custom functions for distance calculations
-- (Distance from Aviano AB gates in km)

-- Gate coordinates (approximate)
-- Gate F16: 46.0319° N, 12.5965° E
-- Gate Main: 46.0342° N, 12.5989° E

CREATE OR REPLACE FUNCTION calculate_distance_to_gate_f16(lat DOUBLE PRECISION, lon DOUBLE PRECISION)
RETURNS DOUBLE PRECISION AS $$
BEGIN
    RETURN ST_Distance(
        ST_GeographyFromText('POINT(12.5965 46.0319)'),
        ST_GeographyFromText('POINT(' || lon || ' ' || lat || ')')
    ) / 1000; -- Convert meters to km
END;
$$ LANGUAGE plpgsql IMMUTABLE;

CREATE OR REPLACE FUNCTION calculate_distance_to_gate_main(lat DOUBLE PRECISION, lon DOUBLE PRECISION)
RETURNS DOUBLE PRECISION AS $$
BEGIN
    RETURN ST_Distance(
        ST_GeographyFromText('POINT(12.5989 46.0342)'),
        ST_GeographyFromText('POINT(' || lon || ' ' || lat || ')')
    ) / 1000; -- Convert meters to km
END;
$$ LANGUAGE plpgsql IMMUTABLE;

-- Set default timezone
SET timezone = 'Europe/Rome';

-- Show enabled extensions
SELECT extname, extversion FROM pg_extension;
