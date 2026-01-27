# JaraMarket CI/CD Setup

This repository uses GitHub Actions for automated deployment to AWS.

## Required GitHub Secrets

Add these secrets to your GitHub repository (Settings → Secrets and variables → Actions):

| Secret Name | Description | How to get it |
|-------------|-------------|---------------|
| `AWS_ACCESS_KEY_ID` | AWS IAM user access key | Create IAM user with ECR + ECS permissions |
| `AWS_SECRET_ACCESS_KEY` | AWS IAM user secret key | From the same IAM user |

## IAM Permissions Required

Create an IAM user named `jaramarket-github-actions` with these permissions:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "ecr:GetAuthorizationToken",
        "ecr:BatchCheckLayerAvailability",
        "ecr:GetDownloadUrlForLayer",
        "ecr:BatchGetImage",
        "ecr:PutImage",
        "ecr:InitiateLayerUpload",
        "ecr:UploadLayerPart",
        "ecr:CompleteLayerUpload"
      ],
      "Resource": "*"
    },
    {
      "Effect": "Allow",
      "Action": [
        "ecs:DescribeServices",
        "ecs:DescribeTaskDefinition",
        "ecs:DescribeTasks",
        "ecs:ListTasks",
        "ecs:RegisterTaskDefinition",
        "ecs:UpdateService"
      ],
      "Resource": "*"
    },
    {
      "Effect": "Allow",
      "Action": [
        "iam:PassRole"
      ],
      "Resource": "arn:aws:iam::016491066247:role/jaramarketEcsTaskExecutionRole"
    }
  ]
}
```

## Deployment Workflow

The deployment automatically triggers when you push to the `fix/product-create` branch:

```bash
git add .
git commit -m "Deploy to production"
git push origin fix/product-create
```

Or trigger manually from GitHub Actions tab → "Deploy JaraMarket to AWS" → "Run workflow"

## What the Pipeline Does

1. **Build Stage:**
   - Checks out code
   - Builds Docker image using Dockerfile
   - Tags with git SHA and `latest`
   - Pushes to ECR repository: `jaramarket-laravel`

2. **Deploy Stage:**
   - Downloads current ECS task definition
   - Updates image tag to new version
   - Registers new task definition
   - Updates ECS service: `jaramarket-api`
   - Waits for deployment to stabilize
   - Verifies all tasks are running healthy
   - Auto-rollback on failure

## Infrastructure Details

- **ECS Cluster:** `jaramarket-cluster`
- **ECS Service:** `jaramarket-api`
- **ECR Repository:** `jaramarket-laravel`
- **Load Balancer:** `jaramarket-alb`
- **Health Check:** `/ ` (accepts HTTP 200 or 302)
- **AWS Region:** `us-east-1`

## Monitoring Deployment

1. Go to GitHub Actions tab
2. Click on the running workflow
3. Watch build and deploy steps in real-time
4. Check ECS service in AWS Console for task status

##Troubleshooting

### Deployment Fails with "Unhealthy"
- Check ECS task logs in CloudWatch: `/ecs/jaramarket-laravel`
- Verify ALB target group health checks passing
- Ensure environment variables in Secrets Manager are correct

### Build Fails
- Check Dockerfile syntax
- Verify all dependencies in composer.json are valid
- Check GitHub Actions logs for specific error

### Permission Errors
- Verify IAM user has all required permissions
- Check GitHub secrets are correctly set

## Health Check Configuration

The application uses Apache on port 80. Health checks hit the root path `/` which redirects to login (HTTP 302), both 200 and 302 are considered healthy.

## Auto-Scaling

- Minimum tasks: 2
- Maximum tasks: 10
- Scale-out trigger: CPU > 70% OR Memory > 80%
- Scale-in cooldown: 5 minutes

## Manual Deployment (if needed)

```bash
# Login to ECR
aws ecr get-login-password --region us-east-1 --profile jaramarket | docker login --username AWS --password-stdin 016491066247.dkr.ecr.us-east-1.amazonaws.com

# Build and push
docker build -t jaramarket-laravel .
docker tag jaramarket-laravel:latest 016491066247.dkr.ecr.us-east-1.amazonaws.com/jaramarket-laravel:latest
docker push 016491066247.dkr.ecr.us-east-1.amazonaws.com/jaramarket-laravel:latest

# Force new deployment
aws ecs update-service --cluster jaramarket-cluster --service jaramarket-api --force-new-deployment --profile jaramarket --region us-east-1
```
