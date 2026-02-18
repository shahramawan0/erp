<?php

require_once __DIR__ . '/BaseModel.php';

class StoreOpeningStock extends BaseModel
{
    protected $table = 'stock_receives';

    /**
     * Get the next voucher number for store opening stock
     */
    public function getNextVoucherNumber()
    {
        try {
            $sql = "SELECT MAX(CAST(voucher_no AS UNSIGNED)) as max_voucher FROM {$this->table} WHERE is_deleted = 0 AND transaction_type = 'opening'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $maxVoucher = $result['max_voucher'] ?? 0;
            $nextVoucher = $maxVoucher + 1;

            error_log("StoreOpeningStock::getNextVoucherNumber() - Next voucher number: " . $nextVoucher);

            return $nextVoucher;
        } catch (Exception $e) {
            error_log("StoreOpeningStock::getNextVoucherNumber() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get units list for dropdown
     */
    public function getUnits()
    {
        try {
            $sql = "SELECT id, name FROM units WHERE is_deleted = 0 ORDER BY name ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("StoreOpeningStock::getUnits() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get items list for dropdown (limited to specified number)
     * Fetches rack_id from item_rack_assignments table based on unit_id
     */
    public function getItems($limit = 50, $unitId = null)
    {
        try {
            $companyId = $this->getCompanyId();
            
            if ($unitId) {
                // Join with item_rack_assignments to get rack_id based on unit_id, and unit_types for unit_type_name
                $sql = "SELECT 
                            i.id, 
                            i.source_id, 
                            i.name, 
                            ira.rack_id,
                            ut.name AS unit_type_name
                        FROM items i
                        LEFT JOIN item_rack_assignments ira ON i.id = ira.item_id 
                            AND ira.unit_id = :unit_id 
                            AND ira.company_id = :company_id
                            AND ira.is_primary = 1
                            AND ira.is_deleted = 0 
                            AND ira.status = 'A'
                        LEFT JOIN unit_types ut ON i.unit_type_id = ut.id AND ut.is_deleted = 0
                        WHERE i.is_deleted = 0 
                            AND i.status = 'I' 
                        ORDER BY i.source_id ASC 
                        LIMIT :limit";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(':unit_id', (int)$unitId, PDO::PARAM_INT);
                $stmt->bindValue(':company_id', (int)$companyId, PDO::PARAM_INT);
            } else {
                // If no unit_id provided, return items without rack_id
                $sql = "SELECT i.id, i.source_id, i.name, NULL as rack_id, ut.name AS unit_type_name FROM items i LEFT JOIN unit_types ut ON i.unit_type_id = ut.id AND ut.is_deleted = 0 WHERE i.is_deleted = 0 AND i.status = 'I' ORDER BY i.source_id ASC LIMIT :limit";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("StoreOpeningStock::getItems() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Search items by source_id or name
     * Fetches rack_id from item_rack_assignments table based on unit_id
     */
    public function searchItems($search, $unitId = null)
    {
        try {
            $companyId = $this->getCompanyId();
            
            if ($unitId) {
                // Join with item_rack_assignments to get rack_id based on unit_id, and unit_types for unit_type_name
                $sql = "SELECT 
                            i.id, 
                            i.source_id, 
                            i.name, 
                            ira.rack_id,
                            ut.name AS unit_type_name
                        FROM items i
                        LEFT JOIN item_rack_assignments ira ON i.id = ira.item_id 
                            AND ira.unit_id = :unit_id 
                            AND ira.company_id = :company_id
                            AND ira.is_primary = 1
                            AND ira.is_deleted = 0 
                            AND ira.status = 'A'
                        LEFT JOIN unit_types ut ON i.unit_type_id = ut.id AND ut.is_deleted = 0
                        WHERE i.is_deleted = 0 
                            AND i.status = 'I' 
                            AND (CAST(i.source_id AS CHAR) LIKE :search OR i.name LIKE :search)
                        ORDER BY i.source_id ASC 
                        LIMIT 100";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                $stmt->bindValue(':unit_id', (int)$unitId, PDO::PARAM_INT);
                $stmt->bindValue(':company_id', (int)$companyId, PDO::PARAM_INT);
            } else {
                // If no unit_id provided, return items without rack_id
                $sql = "SELECT i.id, i.source_id, i.name, NULL as rack_id, ut.name AS unit_type_name FROM items i LEFT JOIN unit_types ut ON i.unit_type_id = ut.id AND ut.is_deleted = 0 WHERE i.is_deleted = 0 AND i.status = 'I' AND (CAST(i.source_id AS CHAR) LIKE :search OR i.name LIKE :search) ORDER BY i.source_id ASC LIMIT 100";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("StoreOpeningStock::searchItems() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get racks list for dropdown filtered by unit_id
     */
    public function getRacks($unitId = null)
    {
        try {
            $companyId = $this->getCompanyId();
            
            if ($unitId) {
                // Filter racks by unit_id
                $sql = "SELECT id, name FROM racks WHERE is_deleted = 0 AND company_id = ? AND unit_id = ? ORDER BY name ASC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$companyId, $unitId]);
            } else {
                // If no unit_id provided, return all racks (fallback)
                $sql = "SELECT id, name FROM racks WHERE is_deleted = 0 AND company_id = ? ORDER BY name ASC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$companyId]);
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("StoreOpeningStock::getRacks() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if item with rack already exists in opening stock
     */
    public function checkExistingOpeningStock($itemId, $rackId)
    {
        try {
            $companyId = $this->getCompanyId();
            $sql = "SELECT sr.voucher_no 
                    FROM stock_receives sr 
                    WHERE sr.item_id = ? 
                    AND sr.rack_id = ? 
                    AND sr.company_id = ? 
                    AND sr.status = 'opening'
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$itemId, $rackId, $companyId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result;
        } catch (Exception $e) {
            error_log("StoreOpeningStock::checkExistingOpeningStock() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new store opening stock entry
     */
    public function create($data)
    {
        try {
            // Begin transaction
            $this->db->beginTransaction();

            // Auto-generate voucher number if not provided
            if (!isset($data['voucher_no']) || empty($data['voucher_no'])) {
                $data['voucher_no'] = $this->getNextVoucherNumber();
                error_log("StoreOpeningStock::create() - Auto-generated voucher number: " . $data['voucher_no']);
            }

            $insertedIds = [];

            // Insert each item as a separate record
            foreach ($data['items'] as $item) {
                $sql = "INSERT INTO {$this->table} (transaction_type, company_id, voucher_no, voucher_date, unit_id, item_id, rack_id, qty, status, remarks, narration, created_at, updated_at) VALUES (:transaction_type, :company_id, :voucher_no, :voucher_date, :unit_id, :item_id, :rack_id, :qty, :status, :remarks, :narration, NOW(), NOW())";

                $params = [
                    ':transaction_type' => 'opening',
                    ':company_id' => $this->getCompanyId(),
                    ':voucher_no' => $data['voucher_no'],
                    ':voucher_date' => $data['voucher_date'],
                    ':unit_id' => $data['unit_id'],
                    ':item_id' => $item['item_id'],
                    ':rack_id' => $item['rack_id'],
                    ':qty' => $item['qty'],
                    ':status' => 'opening',
                    ':remarks' => 'Store Opening Stock Entry',
                    ':narration' => $item['narration'] ?? ($data['narration'] ?? 'Initial stock entry for ' . date('Y-m-d'))
                ];

                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $insertedIds[] = $this->db->lastInsertId();
            }

            // Commit transaction
            $this->db->commit();

            // Return voucher number along with first ID
            return [
                'id' => $insertedIds[0],
                'voucher_no' => $data['voucher_no']
            ];

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            error_log("StoreOpeningStock::create() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get store opening stock listing with filtering
     */
    public function getListing($filters = [], $page = 1, $limit = 10)
    {
        try {
            $companyId = $this->getCompanyId();
            $offset = ($page - 1) * $limit;

            // Build WHERE conditions for opening stock
            $whereConditions = [];
            $params = [];

            // Base conditions for opening stock
            $whereConditions[] = "sr.transaction_type = 'opening'";
            $whereConditions[] = "sr.status = 'opening'";
            $whereConditions[] = "sr.is_deleted = 0";

            // Add company filter
            $whereConditions[] = "sr.company_id = :company_id";
            $params[':company_id'] = $companyId;

            // Add unit filter if provided
            if (!empty($filters['unit_id'])) {
                $whereConditions[] = "sr.unit_id = :unit_id";
                $params[':unit_id'] = $filters['unit_id'];
            }

            // Add date filter if provided
            if (!empty($filters['date'])) {
                $whereConditions[] = "DATE(sr.voucher_date) = :date";
                $params[':date'] = $filters['date'];
            }

            // Add search filter if provided
            if (!empty($filters['search'])) {
                $whereConditions[] = "(sr.voucher_no LIKE :search OR sr.remarks LIKE :search OR sr.narration LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            $whereClause = implode(' AND ', $whereConditions);

            // Main query for opening stock - grouped by voucher
            $sql = "SELECT 
                        sr.voucher_no,
                        sr.voucher_date,
                        sr.unit_id,
                        u.name as unit_name,
                        COUNT(sr.id) as item_count,
                        SUM(sr.qty) as total_qty,
                        GROUP_CONCAT(DISTINCT i.name ORDER BY i.name SEPARATOR ', ') as items_list,
                        GROUP_CONCAT(DISTINCT r.name ORDER BY r.name SEPARATOR ', ') as racks_list
                    FROM {$this->table} sr
                    LEFT JOIN units u ON sr.unit_id = u.id
                    LEFT JOIN items i ON sr.item_id = i.id
                    LEFT JOIN racks r ON sr.rack_id = r.id
                    WHERE {$whereClause}
                    GROUP BY sr.voucher_no, sr.voucher_date, sr.unit_id, u.name
                    ORDER BY sr.voucher_date DESC, sr.voucher_no DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            // Bind all filter parameters first
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            // Bind parameters with explicit types for LIMIT and OFFSET
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debug: Log the query and results
            error_log("StoreOpeningStock::getListing() - SQL: " . $sql);
            error_log("StoreOpeningStock::getListing() - Params: " . json_encode($params));
            error_log("StoreOpeningStock::getListing() - Results count: " . count($results));

            // Get total count for pagination (count of unique vouchers)
            $countSql = "SELECT COUNT(DISTINCT sr.voucher_no) as total FROM {$this->table} sr WHERE {$whereClause}";

            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
            $total = $countResult['total'] ?? 0;

            return [
                'records' => $results,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ];
        } catch (Exception $e) {
            error_log("StoreOpeningStock::getListing() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get voucher details for editing
     */
    public function getVoucherDetails($voucherNo)
    {
        try {
            $companyId = $this->getCompanyId();
            
            // Get voucher header information
            $headerSql = "SELECT 
                            sr.voucher_no,
                            sr.voucher_date,
                            sr.unit_id,
                            u.name as unit_name
                        FROM {$this->table} sr
                        LEFT JOIN units u ON sr.unit_id = u.id
                        WHERE sr.voucher_no = :voucher_no 
                        AND sr.company_id = :company_id 
                        AND sr.transaction_type = 'opening' 
                        AND sr.status = 'opening'
                        AND sr.is_deleted = 0
                        LIMIT 1";

            $headerStmt = $this->db->prepare($headerSql);
            $headerStmt->bindValue(':voucher_no', $voucherNo, PDO::PARAM_STR);
            $headerStmt->bindValue(':company_id', $companyId, PDO::PARAM_INT);
            $headerStmt->execute();
            $header = $headerStmt->fetch(PDO::FETCH_ASSOC);

            if (!$header) {
                throw new Exception('Voucher not found');
            }

            // Get all items for this voucher
            $itemsSql = "SELECT 
                            sr.item_id,
                            sr.rack_id,
                            sr.qty,
                            sr.narration,
                            i.source_id,
                            i.name as item_name,
                            r.name as rack_name
                        FROM {$this->table} sr
                        LEFT JOIN items i ON sr.item_id = i.id AND i.status = 'I'
                        LEFT JOIN racks r ON sr.rack_id = r.id
                        WHERE sr.voucher_no = :voucher_no 
                        AND sr.company_id = :company_id 
                        AND sr.transaction_type = 'opening' 
                        AND sr.status = 'opening'
                        AND sr.is_deleted = 0
                        ORDER BY sr.id ASC";

            $itemsStmt = $this->db->prepare($itemsSql);
            $itemsStmt->bindValue(':voucher_no', $voucherNo, PDO::PARAM_STR);
            $itemsStmt->bindValue(':company_id', $companyId, PDO::PARAM_INT);
            $itemsStmt->execute();
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'voucher_no' => $header['voucher_no'],
                'voucher_date' => $header['voucher_date'],
                'unit_id' => $header['unit_id'],
                'unit_name' => $header['unit_name'],
                'items' => $items
            ];

        } catch (Exception $e) {
            error_log("StoreOpeningStock::getVoucherDetails() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update existing store opening stock entry with custom logic
     */
    public function update($id, $data)
    {
        try {
            $companyId = $this->getCompanyId();
            
            // Add company_id and transaction_type conditions to ensure only opening stock can be updated
            $data['company_id'] = $companyId;
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            // Use parent update method
            $result = parent::update($id, $data);
            
            if ($result) {
                error_log("StoreOpeningStock::update() - Successfully updated record ID: " . $id);
                return true;
            } else {
                error_log("StoreOpeningStock::update() - Failed to update record ID: " . $id);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("StoreOpeningStock::update() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update entire voucher with all its items
     */
    public function updateVoucher($data)
    {
        try {
            $companyId = $this->getCompanyId();
            
            // Start transaction
            $this->db->beginTransaction();
            
            try {
                // Update voucher header (date and unit)
                $headerSql = "UPDATE {$this->table} SET 
                                voucher_date = :voucher_date,
                                unit_id = :unit_id,
                                updated_at = NOW()
                            WHERE voucher_no = :voucher_no 
                            AND company_id = :company_id 
                            AND transaction_type = 'opening' 
                            AND status = 'opening'
                            AND is_deleted = 0";

                $headerStmt = $this->db->prepare($headerSql);
                $headerStmt->bindValue(':voucher_date', $data['voucher_date'], PDO::PARAM_STR);
                $headerStmt->bindValue(':unit_id', $data['unit_id'], PDO::PARAM_INT);
                $headerStmt->bindValue(':voucher_no', $data['voucher_no'], PDO::PARAM_STR);
                $headerStmt->bindValue(':company_id', $companyId, PDO::PARAM_INT);
                $headerStmt->execute();

                // First, mark all existing items as deleted
                $markDeletedSql = "UPDATE {$this->table} SET 
                                    is_deleted = 1,
                                    updated_at = NOW()
                                  WHERE voucher_no = :voucher_no 
                                  AND company_id = :company_id 
                                  AND transaction_type = 'opening' 
                                  AND status = 'opening'
                                  AND is_deleted = 0";
                
                $markDeletedStmt = $this->db->prepare($markDeletedSql);
                $markDeletedStmt->bindValue(':voucher_no', $data['voucher_no'], PDO::PARAM_STR);
                $markDeletedStmt->bindValue(':company_id', $companyId, PDO::PARAM_INT);
                $markDeletedStmt->execute();
                
                // Now insert all items as new records
                foreach ($data['items'] as $index => $item) {
                    error_log("StoreOpeningStock::updateVoucher() - Processing item {$index}: " . json_encode($item));
                    
                    $insertSql = "INSERT INTO {$this->table} (
                                    voucher_no, voucher_date, unit_id, item_id, rack_id, 
                                    qty, narration, company_id, transaction_type, status, 
                                    created_at, updated_at
                                ) VALUES (
                                    :voucher_no, :voucher_date, :unit_id, :item_id, :rack_id,
                                    :qty, :narration, :company_id, 'opening', 'opening',
                                    NOW(), NOW()
                                )";
                    
                    $insertStmt = $this->db->prepare($insertSql);
                    $insertStmt->bindValue(':voucher_no', $data['voucher_no'], PDO::PARAM_STR);
                    $insertStmt->bindValue(':voucher_date', $data['voucher_date'], PDO::PARAM_STR);
                    $insertStmt->bindValue(':unit_id', $data['unit_id'], PDO::PARAM_INT);
                    $insertStmt->bindValue(':item_id', $item['item_id'], PDO::PARAM_INT);
                    $insertStmt->bindValue(':rack_id', $item['rack_id'], PDO::PARAM_INT);
                    $insertStmt->bindValue(':qty', $item['qty'], PDO::PARAM_STR);
                    $insertStmt->bindValue(':narration', $item['narration'] ?? '', PDO::PARAM_STR);
                    $insertStmt->bindValue(':company_id', $companyId, PDO::PARAM_INT);
                    
                    $result = $insertStmt->execute();
                    error_log("StoreOpeningStock::updateVoucher() - Insert result for item {$index}: " . ($result ? 'SUCCESS' : 'FAILED'));
                    
                    if (!$result) {
                        $errorInfo = $insertStmt->errorInfo();
                        error_log("StoreOpeningStock::updateVoucher() - Insert error for item {$index}: " . json_encode($errorInfo));
                    }
                }

                // Commit transaction
                $this->db->commit();
                
                error_log("StoreOpeningStock::updateVoucher() - Successfully updated voucher: " . $data['voucher_no']);
                return true;
                
            } catch (Exception $e) {
                // Rollback transaction on error
                $this->db->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("StoreOpeningStock::updateVoucher() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete entire voucher with all its items
     */
    public function deleteVoucher($voucherNo)
    {
        try {
            $companyId = $this->getCompanyId();
            
            // Start transaction
            $this->db->beginTransaction();
            
            try {
                // Mark all items in the voucher as deleted
                $deleteSql = "UPDATE {$this->table} SET 
                                is_deleted = 1,
                                updated_at = NOW()
                              WHERE voucher_no = :voucher_no 
                              AND company_id = :company_id 
                              AND transaction_type = 'opening' 
                              AND status = 'opening'
                              AND is_deleted = 0";
                
                $deleteStmt = $this->db->prepare($deleteSql);
                $deleteStmt->bindValue(':voucher_no', $voucherNo, PDO::PARAM_STR);
                $deleteStmt->bindValue(':company_id', $companyId, PDO::PARAM_INT);
                $deleteStmt->execute();
                
                // Commit transaction
                $this->db->commit();
                
                error_log("StoreOpeningStock::deleteVoucher() - Successfully deleted voucher: " . $voucherNo);
                return true;
                
            } catch (Exception $e) {
                // Rollback transaction on error
                $this->db->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("StoreOpeningStock::deleteVoucher() - Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get company ID from session or default
     */
    private function getCompanyId()
    {
        // For now, return default company ID 1
        // In production, this should come from user session
        return 1;
    }
}


