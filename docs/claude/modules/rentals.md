# Rentals

| Feature                                                     | Screens         | Code                                |
| ----------------------------------------------------------- | --------------- | ----------------------------------- |
| Fleet, units, availability calendar, no double booking      | Rentals         | `Availability`, `ReserveRental`     |
| Cheapest mix of hour/day/week/month rates; free Sunday rule | Rental quote    | `RentalPricer`, `RentalEstimator`   |
| Agreements signed in person or from a link                  | Rental page     | `PrepareAgreement`, `SignAgreement` |
| Check-out/check-in with hour meter and inspection           | Rental page     | `CheckOutRental`, `CheckInRental`   |
| Service intervals that lock units out                       | Maintenance     | `Maintenance`                       |
| Deposits and payments                                       | Rental payments | `app/Rentals/Payments`              |
| Online booking                                              | `/book/{yard}`  | `Rentals/BookingController`         |
| Reminder texts and emails                                   | (scheduled)     | `yardrunner:rentals:remind`         |
