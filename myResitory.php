<?php   


/**
     * @inheritdoc
     */
    public function findByPK(int $id): ?SMSTemplate
    {
        return SMSTemplate::model()->findByPk($id);
    }

    /**
     * @inheritdoc
     */
    public function getCount(): int
    {
        $sql = 'SELECT * FROM `sms_template`';
        $query = $this->pdo->prepare($sql);
        $query->execute();

        return $query->rowCount();
    }

    /**
     * @inheritdoc
     */
    public function getCountByDepartment(string $department): int
    {
        $sql = 'SELECT * FROM `sms_template` WHERE `department` = :department';
        $query = $this->pdo->prepare($sql);
        $query->execute([':department' => $department]);

        return $query->rowCount();
    }
    
       /**
     * @inheritdoc
     */
    public function getByDepartmentAndSortLimitOffset(
        string $department,
        int    $limit,
        int    $offset,
        string $sort,
        string $direction,
    ): array {
        $sql = 'SELECT * FROM `sms_template` WHERE department = :department 
                ORDER BY :sort :direction LIMIT ' . $limit . ' OFFSET ' . $offset;

        $params = [
            ':department' => $department,
            ':sort' => $sort,
            ':direction' => $direction,
        ];

        $query = $this->pdo->prepare($sql);
        $query->execute($params);

        return $query->fetchAll();
    }

    /**
     * @inheritdoc
     */
    public function getBySortLimitOffset(
        int    $limit,
        int    $offset,
        string $sort,
        string $direction,
    ): array {
        $sql = 'SELECT * FROM `sms_template` ORDER BY :sort :direction
                LIMIT ' . $limit . ' OFFSET ' . $offset;

        $params = [
            ':sort' => $sort,
            ':direction' => $direction,
        ];

        $query = $this->pdo->prepare($sql);
        $query->execute($params);

        return $query->fetchAll();
    }



    /**
     * Метод принимает собраную DTO, класса CommentSearchDTO. Обрабатывает все параметры фильтрации внутри ее.
     * Возвращает объект класса Select
     *
     * @param CommentSearchDTO $commentSearchDTO - собранная DTO с параметрами фильтрации
     * @return Select
     */
    protected function selectWithRelations(CommentSearchDTO $commentSearchDTO): Select
    {
        $params = [];
        if (null !== $commentSearchDTO->getDate()) {
            $params['timestamp'] = ['between' => [
                $commentSearchDTO->getDate()->setTime(0, 0)->format(DateFmt::DT_DB),
                $commentSearchDTO->getDate()->setTime(23, 59)->format(DateFmt::DT_DB)]];
        }
        if (null !== $commentSearchDTO->getStaffName()) {
            $params['staff.name'] = ['like' => '%' . $commentSearchDTO->getStaffName() . '%'];
        }
        if (null !== $commentSearchDTO->getRole()) {
            $params['staff.role'] = ['=' => $commentSearchDTO->getRole()];
        }
        if (null !== $commentSearchDTO->getSubString()) {
            $params['content'] = ['like' => '%' . $commentSearchDTO->getSubString() . '%'];
        }

        return $this->select()->where($params)->load('staff');
    }
