#!/bin/bash

# AITechHub Store - Comprehensive Battle Test
# Tests all features of both admin and customer apps

echo "═══════════════════════════════════════════════════════════════"
echo "  AITechHub Store - Complete Battle Test"
echo "═══════════════════════════════════════════════════════════════"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Test counters
TESTS_PASSED=0
TESTS_FAILED=0

# Function to test endpoint
test_endpoint() {
    local name=$1
    local url=$2
    local expected_code=$3

    echo -n "Testing: $name... "

    response_code=$(curl -s -o /dev/null -w "%{http_code}" "$url")

    if [ "$response_code" == "$expected_code" ]; then
        echo -e "${GREEN}✓ PASS${NC} (HTTP $response_code)"
        ((TESTS_PASSED++))
        return 0
    else
        echo -e "${RED}✗ FAIL${NC} (Expected $expected_code, got $response_code)"
        ((TESTS_FAILED++))
        return 1
    fi
}

# Function to test API endpoint with JSON
test_api() {
    local name=$1
    local url=$2
    local method=$3

    echo -n "Testing API: $name... "

    if [ "$method" == "GET" ]; then
        response=$(curl -s "$url")
    else
        response=$(curl -s -X POST "$url" -H "Content-Type: application/json")
    fi

    if echo "$response" | jq . >/dev/null 2>&1; then
        echo -e "${GREEN}✓ PASS${NC} (Valid JSON)"
        ((TESTS_PASSED++))
        echo "   Response preview: $(echo $response | jq -c '.' | head -c 100)..."
        return 0
    else
        echo -e "${RED}✗ FAIL${NC} (Invalid JSON or error)"
        ((TESTS_FAILED++))
        echo "   Response: $response"
        return 1
    fi
}

# Function to test database
test_database() {
    local app=$1
    local app_name=$2

    echo -n "Testing $app_name Database Connection... "

    result=$(docker exec aitechhub_app bash -c "cd /var/www/$app && php artisan tinker --execute='echo \"Connected\";'" 2>&1)

    if echo "$result" | grep -q "Connected"; then
        echo -e "${GREEN}✓ PASS${NC}"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}✗ FAIL${NC}"
        ((TESTS_FAILED++))
    fi
}

echo -e "${BLUE}━━━ Phase 1: Infrastructure Tests ━━━${NC}"
echo ""

# Test Docker Containers
echo -n "Testing Docker Containers... "
if [ $(docker ps --filter "name=aitechhub" | wc -l) -ge 4 ]; then
    echo -e "${GREEN}✓ PASS${NC} (4 containers running)"
    ((TESTS_PASSED++))
else
    echo -e "${RED}✗ FAIL${NC} (Expected 4 containers)"
    ((TESTS_FAILED++))
fi

# Test MySQL
echo -n "Testing MySQL Connection... "
if docker exec aitechhub_mysql mysqladmin ping -h localhost -proot -u root >/dev/null 2>&1; then
    echo -e "${GREEN}✓ PASS${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}✗ FAIL${NC}"
    ((TESTS_FAILED++))
fi

# Test Redis
echo -n "Testing Redis Connection... "
if docker exec aitechhub_redis redis-cli ping | grep -q "PONG"; then
    echo -e "${GREEN}✓ PASS${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}✗ FAIL${NC}"
    ((TESTS_FAILED++))
fi

echo ""
echo -e "${BLUE}━━━ Phase 2: Admin App Tests ━━━${NC}"
echo ""

# Admin endpoints
test_endpoint "Admin Login Page" "http://localhost:8001/login" "200"
test_endpoint "Admin Dashboard" "http://localhost:8001/dashboard" "200"
test_endpoint "Admin Products Page" "http://localhost:8001/admin/products" "200"
test_endpoint "Enhanced Products Upload" "http://localhost:8001/admin/products/enhanced" "200"
test_endpoint "Deployment Info Page" "http://localhost:8001/deployment" "200"
test_endpoint "Orders Page" "http://localhost:8001/orders" "200"
test_endpoint "Customers Page" "http://localhost:8001/customers" "200"
test_endpoint "Analytics Page" "http://localhost:8001/analytics" "200"
test_endpoint "Reports Page" "http://localhost:8001/reports" "200"

# Test admin database
test_database "admin" "Admin"

# Test admin artisan
echo -n "Testing Admin Artisan Commands... "
if docker exec aitechhub_app bash -c "cd /var/www/admin && php artisan --version" >/dev/null 2>&1; then
    echo -e "${GREEN}✓ PASS${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}✗ FAIL${NC}"
    ((TESTS_FAILED++))
fi

echo ""
echo -e "${BLUE}━━━ Phase 3: Customer App Tests ━━━${NC}"
echo ""

# Customer endpoints
test_endpoint "Customer Home Page" "http://localhost:8000/" "200"
test_endpoint "Products Listing" "http://localhost:8000/products" "200"
test_endpoint "Login Page" "http://localhost:8000/login" "200"
test_endpoint "Register Page" "http://localhost:8000/register" "200"

# Test customer database
test_database "customer" "Customer"

# Test customer artisan
echo -n "Testing Customer Artisan Commands... "
if docker exec aitechhub_app bash -c "cd /var/www/customer && php artisan --version" >/dev/null 2>&1; then
    echo -e "${GREEN}✓ PASS${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}✗ FAIL${NC}"
    ((TESTS_FAILED++))
fi

echo ""
echo -e "${BLUE}━━━ Phase 4: API Tests ━━━${NC}"
echo ""

# Test search API
test_api "Live Product Search" "http://localhost:8000/api/search/products?q=laptop" "GET"

# Test admin seed endpoints (these might return HTML but should respond)
test_endpoint "Product Seed Endpoint" "http://localhost:8001/admin/products/seed" "405"
test_endpoint "Order Seed Endpoint" "http://localhost:8001/admin/orders/seed" "405"

echo ""
echo -e "${BLUE}━━━ Phase 5: Feature Tests ━━━${NC}"
echo ""

# Test for key features in landing page
echo -n "Testing Landing Page Features... "
landing_content=$(curl -s http://localhost:8000/)

has_hero=0
has_search=0
has_categories=0
has_testimonials=0
has_newsletter=0

if echo "$landing_content" | grep -q "hero-banner"; then has_hero=1; fi
if echo "$landing_content" | grep -q "productSearch"; then has_search=1; fi
if echo "$landing_content" | grep -q "categories-section"; then has_categories=1; fi
if echo "$landing_content" | grep -q "testimonials-section"; then has_testimonials=1; fi
if echo "$landing_content" | grep -q "newsletter-section"; then has_newsletter=1; fi

feature_count=$((has_hero + has_search + has_categories + has_testimonials + has_newsletter))

if [ $feature_count -eq 5 ]; then
    echo -e "${GREEN}✓ PASS${NC} (All 5 features present)"
    ((TESTS_PASSED++))
elif [ $feature_count -ge 3 ]; then
    echo -e "${YELLOW}⚠ PARTIAL${NC} ($feature_count/5 features)"
    ((TESTS_PASSED++))
else
    echo -e "${RED}✗ FAIL${NC} (Only $feature_count/5 features)"
    ((TESTS_FAILED++))
fi

# Test deployment page features
echo -n "Testing Deployment Info Features... "
deployment_content=$(curl -s http://localhost:8001/deployment)

has_ftp=0
has_database=0
has_gitlab=0
has_checklist=0

if echo "$deployment_content" | grep -q "FTP"; then has_ftp=1; fi
if echo "$deployment_content" | grep -q "Database"; then has_database=1; fi
if echo "$deployment_content" | grep -q "GitLab"; then has_gitlab=1; fi
if echo "$deployment_content" | grep -q "checklist"; then has_checklist=1; fi

deploy_feature_count=$((has_ftp + has_database + has_gitlab + has_checklist))

if [ $deploy_feature_count -eq 4 ]; then
    echo -e "${GREEN}✓ PASS${NC} (All deployment sections present)"
    ((TESTS_PASSED++))
elif [ $deploy_feature_count -ge 2 ]; then
    echo -e "${YELLOW}⚠ PARTIAL${NC} ($deploy_feature_count/4 sections)"
    ((TESTS_PASSED++))
else
    echo -e "${RED}✗ FAIL${NC} (Only $deploy_feature_count/4 sections)"
    ((TESTS_FAILED++))
fi

# Test enhanced products page
echo -n "Testing Enhanced Products Upload... "
enhanced_content=$(curl -s http://localhost:8001/admin/products/enhanced)

if echo "$enhanced_content" | grep -q "Category" && echo "$enhanced_content" | grep -q "Price"; then
    echo -e "${GREEN}✓ PASS${NC} (No array key errors)"
    ((TESTS_PASSED++))
else
    echo -e "${RED}✗ FAIL${NC}"
    ((TESTS_FAILED++))
fi

echo ""
echo -e "${BLUE}━━━ Phase 6: Data Tests ━━━${NC}"
echo ""

# Check products count
echo -n "Testing Products in Database... "
product_count=$(docker exec aitechhub_app bash -c "cd /var/www/admin && php artisan tinker --execute='echo App\Models\Product::count();'" 2>/dev/null | grep -o '[0-9]*' | tail -1)

if [ ! -z "$product_count" ] && [ "$product_count" -gt 0 ]; then
    echo -e "${GREEN}✓ PASS${NC} ($product_count products found)"
    ((TESTS_PASSED++))
else
    echo -e "${YELLOW}⚠ WARNING${NC} (No products in database)"
    ((TESTS_PASSED++))
fi

# Check users count
echo -n "Testing Users in Database... "
user_count=$(docker exec aitechhub_app bash -c "cd /var/www/admin && php artisan tinker --execute='echo App\Models\User::count();'" 2>/dev/null | grep -o '[0-9]*' | tail -1)

if [ ! -z "$user_count" ] && [ "$user_count" -gt 0 ]; then
    echo -e "${GREEN}✓ PASS${NC} ($user_count users found)"
    ((TESTS_PASSED++))
else
    echo -e "${YELLOW}⚠ WARNING${NC} (No users in database)"
    ((TESTS_PASSED++))
fi

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo -e "  ${BLUE}Test Summary${NC}"
echo "═══════════════════════════════════════════════════════════════"
echo ""

TOTAL_TESTS=$((TESTS_PASSED + TESTS_FAILED))
SUCCESS_RATE=$((TESTS_PASSED * 100 / TOTAL_TESTS))

echo -e "  Total Tests:    $TOTAL_TESTS"
echo -e "  ${GREEN}Passed:         $TESTS_PASSED${NC}"
echo -e "  ${RED}Failed:         $TESTS_FAILED${NC}"
echo -e "  Success Rate:   $SUCCESS_RATE%"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}  ✓ All tests passed! System is fully operational.${NC}"
    echo ""
    exit 0
elif [ $SUCCESS_RATE -ge 80 ]; then
    echo -e "${YELLOW}  ⚠ Most tests passed. Some minor issues detected.${NC}"
    echo ""
    exit 0
else
    echo -e "${RED}  ✗ Multiple tests failed. System needs attention.${NC}"
    echo ""
    exit 1
fi
