<?php

declare(strict_types=1);

namespace App\Domain\Model;

enum ProjectType: string
{
    case Reforestation = 'Reflorestamento: Plantação de árvores em áreas que foram desmatadas para aumentar a absorção de CO2.';
    case Afforestation = 'Aflorestamento: Plantação de árvores em áreas que não eram anteriormente florestadas.';
    case Agroforestry = 'Agroflorestamento: Integração de árvores e arbustos em sistemas agrícolas para aumentar a absorção de carbono e a biodiversidade.';
    case ForestConservation = 'ConservacaoFlorestal: Proteção de florestas existentes para prevenir desmatamento e degradação.';
    case RenewableEnergy = 'EnergiaRenovavel: Projetos que geram energia a partir de fontes renováveis como solar, eólica e hidroelétrica.';
    case EnergyEfficiency = 'EficiênciaEnergetica: Iniciativas para melhorar a eficiência no uso de energia em edifícios, indústrias e transporte.';
    case MethaneCapture = 'Captura e utilização de metano de aterros sanitários, plantas de tratamento de esgoto e outras fontes.';
    case SoilCarbonSequestration = 'Práticas agrícolas que aumentam a quantidade de carbono armazenado no solo.';
    case ImprovedCookstoves = 'Fogoes Melhorados: Distribuição de fogões melhorados que são mais eficientes no uso de biomassa e emitem menos CO2.';
    case FuelSwitching = 'Mudanca de Combustivel: Substituição de combustíveis fósseis mais intensivos em carbono por outros que emitem menos carbono.';
    case Bioenergy = 'Bioenergia: Produção de energia a partir de biomassa, incluindo a fermentação de resíduos agrícolas e florestais.';
    case BlueCarbon = 'Carbono Azul: Conservação e restauração de ecossistemas costeiros e marinhos que têm alta capacidade de armazenar carbono.';
    case CarbonCaptureAndStorage = 'Tecnologias que capturam o CO2 emitido por plantas industriais e de energia e o armazenam em formações geológicas subterrâneas.';
    case SustainableAgriculture = 'Agricultura Sustentavel: Implementação de práticas agrícolas sustentáveis que reduzem as emissões de gases de efeito estufa.';
    case UrbanGreening = 'Arborização Urbana: Plantação de árvores e vegetação em áreas urbanas para melhorar a absorção de carbono e fornecer outros benefícios ambientais.';

    public function getDescription(): string
    {
        return $this->value;
    }
}
