<?php

declare(strict_types=1);

namespace Domain\Common;

enum CertificationType: string
{
    case VCS = 'Verified Carbon Standard (VCS) é um dos padrões mais amplamente utilizados para a certificação de projetos de redução de carbono.';
    case CDM = 'Clean Development Mechanism (CDM) é um mecanismo estabelecido pelo Protocolo de Quioto para projetos em países em desenvolvimento.';
    case GoldStandard = 'Gold Standard é uma certificação de alto nível para projetos que visam reduzir emissões e apoiar o desenvolvimento sustentável.';
    case CCBS = 'Climate, Community & Biodiversity Standards (CCBS) certifica projetos que combinam benefícios de clima, comunidade e biodiversidade.';
    case ACR = 'American Carbon Registry (ACR) é um padrão para projetos de redução de carbono, especialmente na América do Norte.';
    case PlanVivo = 'Plan Vivo é uma certificação para projetos de uso da terra que suportam comunidades locais e ambientes sustentáveis.';
    case ISO14064 = 'ISO 14064 é uma norma internacional que especifica princípios e requisitos para a quantificação e relato de reduções de GEE.';
    case SocialCarbon = 'Social Carbon é um padrão que mede os benefícios sociais e ambientais de projetos de carbono.';
    case CarbonFix = 'CarbonFix é uma certificação para projetos florestais que sequestram carbono através do reflorestamento.';
    case VCU = 'Verified Carbon Units (VCU) são créditos emitidos sob o Verified Carbon Standard.';
    case TCR = 'The Climate Registry (TCR) é uma organização sem fins lucrativos que oferece padrões de medição e relato de GEE.';
    case WCC = 'Woodland Carbon Code (WCC) é um padrão do Reino Unido para projetos florestais que sequestram carbono.';
    case REDDPlus = 'REDD+ refere-se a iniciativas para Redução de Emissões por Desmatamento e Degradação Florestal, conservação, manejo sustentável das florestas e aumento dos estoques de carbono florestal.';
    case RCEs = 'Reduções Certificadas de Emissões (RCEs) também conhecidas como créditos de carbono, representam a redução de uma tonelada de dióxido de carbono (CO2) ou outros gases de efeito estufa (GEE).';
    case VERs = 'Reduções Voluntárias de Emissões (VERs) são créditos de carbono negociados em mercados voluntários livres de regulação de terceiros.';
}
