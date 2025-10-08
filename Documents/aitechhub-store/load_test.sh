#!/bin/bash

# AITechHub Store - Load Testing Script
# Simulates concurrent users accessing the application

echo "========================================="
echo "  AITechHub Store - Load Testing"
echo "========================================="
echo ""

# Test Configuration
CONCURRENT_USERS=50
REQUESTS_PER_USER=10
CUSTOMER_URL="http://localhost:8000/"
ADMIN_URL="http://localhost:8001/login"
API_URL="http://localhost:8000/api/search/products?q=demo"

# Create results directory
mkdir -p load_test_results

echo "Test Configuration:"
echo "  Concurrent Users: $CONCURRENT_USERS"
echo "  Requests per User: $REQUESTS_PER_USER"
echo "  Total Requests: $((CONCURRENT_USERS * REQUESTS_PER_USER))"
echo ""

# Test 1: Customer Landing Page
echo "=== Test 1: Customer Landing Page Load Test ==="
echo "Simulating $CONCURRENT_USERS concurrent users..."
echo ""

START_TIME=$(date +%s)
for i in $(seq 1 $CONCURRENT_USERS); do
    {
        for j in $(seq 1 $REQUESTS_PER_USER); do
            curl -s -w "%{time_total}\n" -o /dev/null $CUSTOMER_URL 2>&1
        done
    } &
done
wait
END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

echo "✅ Completed in ${DURATION}s"
echo "   Average: $((DURATION * 1000 / (CONCURRENT_USERS * REQUESTS_PER_USER)))ms per request"
echo ""

# Test 2: Admin Login Page
echo "=== Test 2: Admin Login Page Load Test ==="
echo "Simulating $CONCURRENT_USERS concurrent users..."
echo ""

START_TIME=$(date +%s)
for i in $(seq 1 $CONCURRENT_USERS); do
    {
        for j in $(seq 1 $REQUESTS_PER_USER); do
            curl -s -w "%{time_total}\n" -o /dev/null $ADMIN_URL 2>&1
        done
    } &
done
wait
END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

echo "✅ Completed in ${DURATION}s"
echo "   Average: $((DURATION * 1000 / (CONCURRENT_USERS * REQUESTS_PER_USER)))ms per request"
echo ""

# Test 3: API Endpoint
echo "=== Test 3: Search API Load Test ==="
echo "Simulating $CONCURRENT_USERS concurrent users..."
echo ""

START_TIME=$(date +%s)
for i in $(seq 1 $CONCURRENT_USERS); do
    {
        for j in $(seq 1 $REQUESTS_PER_USER); do
            curl -s -w "%{time_total}\n" -o /dev/null $API_URL 2>&1
        done
    } &
done
wait
END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

echo "✅ Completed in ${DURATION}s"
echo "   Average: $((DURATION * 1000 / (CONCURRENT_USERS * REQUESTS_PER_USER)))ms per request"
echo ""

echo "========================================="
echo "  Load Test Complete"
echo "========================================="
