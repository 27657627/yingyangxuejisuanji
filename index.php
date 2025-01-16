<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>营养学计算器</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
      .form-group {
            display: flex;
            flex-direction: column;
        }
      .form-group label {
            order: 1;
        }
      .form-group input,
      .form-group select {
            order: 2;
        }
      .form-group i {
            order: 0;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
      .alert {
            animation: fadeIn 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>营养学计算器</h1>
        <form action="" method="post">
            <div class="form-group">
                <i class="fas fa-weight-hanging"></i>
                <label for="weight">体重 (kg): </label>
                <input type="number" step="0.01" id="weight" name="weight" class="form-control" value="<?php if (isset($_POST['weight'])) echo $_POST['weight'];?>" required>
            </div>
            <div class="form-group">
                <i class="fas fa-user-secret"></i>
                <label for="height">身高 (cm): </label>
                <input type="number" step="0.01" id="height" name="height" class="form-control" value="<?php if (isset($_POST['height'])) echo $_POST['height'];?>" required>
            </div>
            <div class="form-group">
                <i class="fas fa-user"></i>
                <label for="age">年龄: </label>
                <input type="number" id="age" name="age" class="form-control" value="<?php if (isset($_POST['age'])) echo $_POST['age'];?>" required>
            </div>
            <div class="form-group">
                <i class="fas fa-venus-mars"></i> 
                <label for="gender">性别:</label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="male" <?php if (isset($_POST['gender']) && $_POST['gender'] =='male') echo'selected';?>>男</option>
                    <option value="female" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'female') echo'selected';?>>女</option>
                </select>
            </div>
            <div class="form-group">
                <i class="fas fa-calculator"></i>
                <label for="method">BMR计算方法:</label>
                <select id="method" name="method" class="form-control" required>
                    <option value="weight_based">体重计算法</option>
                    <option value="direct">直接计算法</option>
                </select>
            </div>
            <div class="form-group">
                <i class="fas fa-calendar-day"></i> 
                <label for="fasting_days">轻断食天数:</label>
                <input type="number" id="fasting_days" name="fasting_days" class="form-control" value="<?php if (isset($_POST['fasting_days'])) echo $_POST['fasting_days'];?>" required>
            </div>
            <div class="form-group">
                <i class="fas fa-weight-hanging"></i>
                <label for="target_weight">期望体重 (kg): </label>
                <input type="number" step="0.01" id="target_weight" name="target_weight" class="form-control" value="<?php if (isset($_POST['target_weight'])) echo $_POST['target_weight'];?>" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-calculator"></i> 计算
            </button>
        </form>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $weight = $_POST["weight"];
            $height = $_POST["height"] / 100;
            $age = $_POST["age"];
            $gender = $_POST["gender"];
            $method = $_POST["method"];
            $fasting_days = $_POST["fasting_days"];
            $target_weight = $_POST["target_weight"];

            // 输入验证
            if (is_numeric($weight) && is_numeric($height) && is_numeric($age) && $_POST["height"] > 0 && is_numeric($fasting_days) && is_numeric($target_weight) && $weight > $target_weight) {
                $bmi = $weight / ($height * $height);
                echo '<div class="alert alert-info mt-3" role="alert"><i class="fas fa-chart-line"></i> 你的 BMI 是: '. round($bmi, 2). '</div>';
                
                // 计算每日所需能量
                $daily_energy = 0;
                if ($method == "weight_based") {
                    $daily_energy = ($gender == "male")? 25 * $weight : 20 * $weight;
                } else {
                    if ($gender == "male") {
                        if ($age >= 18 && $age <= 30) {
                            $daily_energy = 2400;
                        } elseif ($age > 30 && $age <= 60) {
                            $daily_energy = 2200;
                        } else {
                            $daily_energy = 2000;
                        }
                    } else {
                        if ($age >= 18 && $age <= 30) {
                            $daily_energy = 2100;
                        } elseif ($age > 30 && $age <= 60) {
                            $daily_energy = 1900;
                        } else {
                            $daily_energy = 1800;
                        }
                    }
                }
                echo '<div class="alert alert-info mt-3" role="alert"><i class="fas fa-bolt"></i> 你每日正常情况下所需的能量是：'. round($daily_energy, 2). '千卡。此能量需求是基于你的基本信息和所选计算方法得出，可作为你日常饮食的参考，但实际能量需求可能因个人的活动水平、身体代谢等因素有所不同。</div>';

                // 轻断食计算，根据不同计算方法调整
                $fasting_energy_min = 0;
                $fasting_energy_max = 0;
                if ($method == "weight_based") {
                    $fasting_energy_min = $daily_energy * 0.3 * $fasting_days;
                    $fasting_energy_max = $daily_energy * 0.4 * $fasting_days;
                } else {
                    $fasting_energy_min = $daily_energy * 0.35 * $fasting_days;
                    $fasting_energy_max = $daily_energy * 0.45 * $fasting_days;
                }
                echo '<div class="alert alert-info mt-3" role="alert"><i class="fas fa-apple-alt"></i> 在 '. $fasting_days. ' 天的轻断食期间，你应摄入的总能量范围为：'. round($fasting_energy_min, 2). '千卡至'. round($fasting_energy_max, 2). '千卡。此范围根据个人的耐受程度和健康状况可调整，具体范围因计算方法不同而有所不同。开始轻断食前，请咨询专业营养师或医生。</div>';

                // 计算bmr
                $total_calorie_deficit = ($weight - $target_weight) * 7700;
                $daily_calorie_deficit = $total_calorie_deficit / $fasting_days;
                $target_daily_energy = $daily_energy - $daily_calorie_deficit;
                echo '<div class="alert alert-info mt-3" role="alert"><i class="fas fa-question-circle"></i> 为在 '. $fasting_days. ' 天内从 '. $weight. 'kg 减到 '. $target_weight. 'kg，你每日应摄入的能量约为：'. round($target_daily_energy, 2). '千卡。请注意，这是一个大致估算，实际情况可能因个体差异而不同。</div>';

                // 根据 BMI 给出健康建议
                if ($bmi < 18.5) {
                    echo '<div class="bmi-container bmi-underweight">';
                    echo '<h2 class="alert-warning"><i class="fas fa-exclamation-triangle"></i> 你的体重过轻。这可能影响身体正常功能，需适当增加营养。</h2>';
                    echo '<div class="alert alert-info mt-3" role="alert"><i class="fas fa-utensils"></i> 建议：<br>- 饮食：<br>  - 早餐：选择富含蛋白质、碳水化合物和健康脂肪的食物，如燕麦粥、全麦面包、鸡蛋、牛奶和坚果。<br>  - 上午加餐：水果奶昔（牛奶、香蕉、蛋白粉混合）或能量棒。<br>  - 午餐：牛排、烤鸡腿、糙米饭、红薯及各种蔬菜。<br>  - 下午加餐：奶酪、水果干或酸奶。<br>  - 晚餐：可适当增加午餐的肉类和碳水化合物量。<br>- 运动：<br>  - 以力量训练为主（举重、深蹲、卧推），每周 3 - 4 次，每次 40 - 60 分钟。<br>  - 结合轻度有氧运动（慢走或瑜伽），每次 20 - 30 分钟。</div>';
                    echo '</div>';
                } elseif ($bmi >= 18.5 && $bmi < 24.9) {
                    echo '<div class="bmi-container bmi-normal">';
                    echo '<h2 class="alert-success"><i class="fas fa-check-circle"></i> 你的体重正常，继续保持健康生活方式很重要。</h2>';
                    echo '<div class="alert alert-info mt-3" role="alert"><i class="fas fa-utensils"></i> 建议：<br>- 饮食：<br>  - 早餐：全麦面包、蔬菜煎蛋、豆浆或酸奶。<br>  - 上午加餐：水果、蔬菜汁或少量坚果。<br>  - 午餐：瘦肉、鱼类、糙米、全麦面条、豆类和大量蔬菜。<br>  - 下午加餐：低脂酸奶或水果。<br>  - 晚餐：类似午餐，可调整食物种类。<br>- 运动：<br>  - 多样化运动：<br>    - 有氧运动（慢跑、游泳、骑自行车），每周 3 - 5 次，每次 30 - 60 分钟。<br>    - 力量训练（平板支撑、引体向上、俯卧撑），每周 2 - 3 次，每次约 30 分钟。</div>';
                    echo '</div>';
                } elseif ($bmi >= 25 && $bmi < 29.9) {
                    echo '<div class="bmi-container bmi-overweight">';
                    echo '<h2 class="alert-warning"><i class="fas fa-exclamation-triangle"></i> 你的体重超重，可能增加健康风险，需控制饮食和增加运动。</h2>';
                    echo '<div class="alert alert-info mt-3" role="alert"><i class="fas fa-utensils"></i> 建议：<br>- 饮食：<br>  - 早餐：蔬菜煎蛋饼、无糖黑咖啡或蔬菜汤。<br>  - 上午加餐：半个苹果、橙子或草莓，或蔬菜沙拉。<br>  - 午餐：多吃蔬菜、瘦肉（鸡肉、鱼肉），搭配少量粗粮。<br>  - 下午加餐：黄瓜、番茄或胡萝卜条。<br>  - 晚餐：蔬菜、少量红薯或玉米及豆腐或水煮虾。<br>- 运动：<br>  - 有氧运动：每天 45 - 60 分钟（快走、慢跑、跳绳、游泳或骑自行车），每周至少 5 天。<br>  - 力量训练：哑铃训练、自重训练（深蹲、弓步蹲），每周 2 - 3 次，每次 20 - 30 分钟。</div>';
                    echo '</div>';
                } else {
                    echo '<div class="bmi-container bmi-obese">';
                    echo '<h2 class="alert-danger"><i class="fas fa-exclamation-circle"></i> 你属于肥胖，需严格的饮食控制和运动计划。</h2>';
                    echo '<div class="alert alert-info mt-3" role="alert"><i class="fas fa-utensils"></i> 建议：<br>- 饮食：<br>  - 早餐：蔬菜汤、水煮蛋或无糖豆浆。<br>  - 上午加餐：饥饿时选少量蔬菜或低卡水果。<br>  - 午餐：大量蔬菜、优质蛋白（烤鸡胸肉、水煮虾、鱼肉）。<br>  - 下午加餐：少量黄瓜、番茄或其他低卡蔬菜。<br>  - 晚餐：蔬菜和少量优质蛋白。<br>- 运动：<br>  - 有氧运动：每天至少 60 分钟（慢跑、游泳、骑自行车），强度可增加。<br>  - 力量训练：30 - 40 分钟（平板支撑、深蹲、俯卧撑），每周 4 - 5 次，在专业教练指导下锻炼。</div>';
                    echo '</div>';
                }
                
                

                   
                   
            } else {
                echo '<div class="alert alert-danger mt-3" role="alert"><i class="fas fa-exclamation-circle"></i> 请输入有效的身高、体重、年龄、轻断食天数和期望体重，且体重应大于期望体重。</div>';
            }
        }
      ?>
    </div>
</body>
</html>