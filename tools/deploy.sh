#!/bin/bash
###############################################################################
# COREGRE - Deploy Helper Script
# Facilita il deploy automatico via GitHub Actions
###############################################################################

set -e  # Exit on error

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funzioni
print_header() {
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}  COREGRE Deploy Helper${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
}

print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

# Check if git is installed
check_git() {
    if ! command -v git &> /dev/null; then
        print_error "Git non è installato!"
        exit 1
    fi
    print_success "Git trovato"
}

# Check if we're in a git repository
check_git_repo() {
    if ! git rev-parse --git-dir > /dev/null 2>&1; then
        print_error "Non sei in un repository Git!"
        exit 1
    fi
    print_success "Repository Git verificato"
}

# Get current branch
get_current_branch() {
    git branch --show-current
}

# Check for uncommitted changes
check_uncommitted_changes() {
    if [ -n "$(git status --porcelain)" ]; then
        print_warning "Hai modifiche non committate:"
        git status --short
        echo ""
        read -p "Vuoi committarle ora? (y/n): " -n 1 -r
        echo ""
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            read -p "Messaggio commit: " commit_msg
            git add .
            git commit -m "$commit_msg"
            print_success "Commit creato!"
        else
            print_error "Deploy annullato. Committa le modifiche prima di deployare."
            exit 1
        fi
    fi
}

# Deploy to staging (developing branch)
deploy_staging() {
    print_header
    print_info "Deploy su STAGING (branch: developing)"
    echo ""

    check_git
    check_git_repo

    current_branch=$(get_current_branch)

    if [ "$current_branch" != "developing" ]; then
        print_warning "Sei sul branch '$current_branch'"
        read -p "Vuoi cambiare a 'developing'? (y/n): " -n 1 -r
        echo ""
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            git checkout developing
            print_success "Cambiato a branch developing"
        else
            print_error "Deploy annullato."
            exit 1
        fi
    fi

    check_uncommitted_changes

    print_info "Push su GitHub..."
    git push origin developing

    print_success "Push completato!"
    echo ""
    print_info "GitHub Actions sta deployando su STAGING..."
    print_info "Controlla: https://github.com/$(git config --get remote.origin.url | sed 's/.*github.com[:/]\(.*\)\.git/\1/')/actions"
}

# Deploy to production (main branch)
deploy_production() {
    print_header
    print_warning "⚠️  DEPLOY SU PRODUZIONE ⚠️"
    echo ""

    check_git
    check_git_repo

    current_branch=$(get_current_branch)

    if [ "$current_branch" != "main" ]; then
        print_warning "Sei sul branch '$current_branch'"
        read -p "Vuoi cambiare a 'main'? (y/n): " -n 1 -r
        echo ""
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            git checkout main
            print_success "Cambiato a branch main"
        else
            print_error "Deploy annullato."
            exit 1
        fi
    fi

    check_uncommitted_changes

    echo ""
    print_warning "Stai per deployare su PRODUZIONE!"
    read -p "Sei SICURO di voler continuare? (yes/no): " confirm

    if [ "$confirm" != "yes" ]; then
        print_error "Deploy annullato."
        exit 1
    fi

    print_info "Merge da developing a main..."
    git merge developing --no-edit || {
        print_error "Errore durante il merge!"
        print_info "Risolvi i conflitti e riprova."
        exit 1
    }

    print_info "Push su GitHub..."
    git push origin main

    print_success "Push completato!"
    echo ""
    print_info "GitHub Actions sta deployando su PRODUZIONE..."
    print_info "Controlla: https://github.com/$(git config --get remote.origin.url | sed 's/.*github.com[:/]\(.*\)\.git/\1/')/actions"
}

# Check GitHub Actions status
check_workflow_status() {
    print_header
    print_info "Aprendo GitHub Actions..."

    repo_url=$(git config --get remote.origin.url | sed 's/.*github.com[:/]\(.*\)\.git/\1/')
    actions_url="https://github.com/${repo_url}/actions"

    # Apri nel browser (funziona su Windows, macOS, Linux)
    if command -v cmd.exe &> /dev/null; then
        # Windows (WSL)
        cmd.exe /c start "$actions_url"
    elif command -v open &> /dev/null; then
        # macOS
        open "$actions_url"
    elif command -v xdg-open &> /dev/null; then
        # Linux
        xdg-open "$actions_url"
    else
        echo "Apri manualmente: $actions_url"
    fi
}

# Show menu
show_menu() {
    print_header
    echo "Cosa vuoi fare?"
    echo ""
    echo "  1) Deploy su STAGING (developing)"
    echo "  2) Deploy su PRODUZIONE (main)"
    echo "  3) Controlla status GitHub Actions"
    echo "  4) Esci"
    echo ""
    read -p "Scelta [1-4]: " choice

    case $choice in
        1)
            deploy_staging
            ;;
        2)
            deploy_production
            ;;
        3)
            check_workflow_status
            ;;
        4)
            print_info "Ciao!"
            exit 0
            ;;
        *)
            print_error "Scelta non valida!"
            exit 1
            ;;
    esac
}

# Main
if [ "$1" == "staging" ]; then
    deploy_staging
elif [ "$1" == "production" ] || [ "$1" == "prod" ]; then
    deploy_production
elif [ "$1" == "status" ]; then
    check_workflow_status
else
    show_menu
fi

echo ""
print_success "Script completato!"
