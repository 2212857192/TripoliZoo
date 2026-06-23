import 'package:flutter_test/flutter_test.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/qr_code_parser.dart';

void main() {
  group('parseAnimalIdentifierFromQr', () {
    test('extracts profile id from visitor URL', () {
      expect(
        parseAnimalIdentifierFromQr('https://zoo.test/app/animals/42'),
        '42',
      );
    });

    test('extracts profile id from URL without scheme', () {
      expect(
        parseAnimalIdentifierFromQr('zoo.test/app/animals/7'),
        '7',
      );
    });

    test('extracts animal code from JSON payload', () {
      expect(
        parseAnimalIdentifierFromQr(
          '{"profile_id":3,"animal_code":"C-001","name":"ليو"}',
        ),
        '3',
      );
    });

    test('falls back to animal code in JSON payload', () {
      expect(
        parseAnimalIdentifierFromQr('{"animal_code":"B-007"}'),
        'B-007',
      );
    });

    test('returns plain animal code unchanged', () {
      expect(parseAnimalIdentifierFromQr('C-001'), 'C-001');
    });

    test('ignores query parameters in visitor URL', () {
      expect(
        parseAnimalIdentifierFromQr(
          'https://zoo.test/app/animals/12?origin=https://zoo.test',
        ),
        '12',
      );
    });

    test('builds in-app details path like website profile URL', () {
      expect(
        animalDetailPathFromQr('https://zoo.test/app/animals/42'),
        '/animals/42',
      );
      expect(animalDetailPathFromQr('C-001'), '/animals/C-001');
      expect(animalDetailPathFromQr(''), isNull);
    });
  });

  group('readBarcodeValue', () {
    test('prefers structured URL bookmark value', () {
      const barcode = Barcode(
        rawValue: 'truncated',
        displayValue: 'truncated',
        url: UrlBookmark(url: 'https://zoo.test/app/animals/5'),
      );

      expect(readBarcodeValue(barcode), 'https://zoo.test/app/animals/5');
    });

    test('falls back to rawValue then displayValue', () {
      const rawOnly = Barcode(rawValue: 'C-009');
      const displayOnly = Barcode(displayValue: 'C-010');

      expect(readBarcodeValue(rawOnly), 'C-009');
      expect(readBarcodeValue(displayOnly), 'C-010');
    });
  });
}
